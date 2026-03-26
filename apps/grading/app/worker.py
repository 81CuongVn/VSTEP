import asyncio
import json

import httpx
from redis.asyncio import Redis

from app.config import settings
from app.grading import grade
from app.logger import logger
from app.models import PermanentError, Task


TASKS_STREAM = settings.grading_queue
GROUP = "grading"
CONSUMER = "grading-1"


async def ensure_groups(redis: Redis):
    try:
        await redis.xgroup_create(TASKS_STREAM, GROUP, id="0", mkstream=True)
    except Exception as e:
        if "BUSYGROUP" not in str(e):
            raise


async def send_result(payload: dict):
    async with httpx.AsyncClient(timeout=30) as client:
        response = await client.post(settings.backend_webhook_url, json=payload)
        response.raise_for_status()


async def process(task: Task, redis: Redis) -> bool:
    for attempt in range(settings.max_retries):
        try:
            result = await grade(task, redis)
            result_dict = json.loads(result.model_dump_json(by_alias=True))
            result_dict["submissionId"] = task.submission_id
            await send_result(result_dict)

            logger.info("graded", submission_id=task.submission_id, score=result.overall_score)
            return True
        except PermanentError:
            logger.error("permanent_failure", submission_id=task.submission_id)
            await send_result({"submissionId": task.submission_id, "failed": True})
            return True
        except Exception as e:
            if attempt == settings.max_retries - 1:
                logger.error("exhausted_retries", submission_id=task.submission_id, error=str(e))
                await send_result({"submissionId": task.submission_id, "failed": True})
                return True
            await asyncio.sleep(2**attempt)
    return False


async def run():
    redis = Redis.from_url(settings.redis_url, decode_responses=True)
    await ensure_groups(redis)
    logger.info("worker_started", stream=TASKS_STREAM)

    try:
        while True:
            try:
                response = await redis.xreadgroup(
                    GROUP, CONSUMER,
                    {TASKS_STREAM: ">"},
                    count=1,
                    block=5000,
                )
                if not response:
                    continue

                for _, messages in response:
                    for message_id, fields in messages:
                        payload = fields.get("payload")
                        if not payload:
                            await redis.xack(TASKS_STREAM, GROUP, message_id)
                            continue

                        try:
                            task = Task.model_validate_json(payload)
                        except Exception as e:
                            logger.error("invalid_task", error=str(e))
                            await redis.xack(TASKS_STREAM, GROUP, message_id)
                            continue

                        success = await process(task, redis)
                        if success:
                            await redis.xack(TASKS_STREAM, GROUP, message_id)
            except Exception as e:
                logger.error("consumer_error", error=str(e))
                await asyncio.sleep(2)
    finally:
        await redis.aclose()
