#!/usr/bin/env python3

import json
import os
import subprocess
import sys
import tempfile
import time
import uuid
from pathlib import Path
from typing import Optional, Tuple

import requests


BASE_URL = os.environ.get("VSTEP_API_URL", "http://5.223.87.142:3000/api/v1")
USER_EMAIL = os.environ.get("VSTEP_SMOKE_EMAIL")
USER_PASSWORD = os.environ.get("VSTEP_SMOKE_PASSWORD", "secret123")

WRITING_CASES = [
    {
        "name": "good",
        "text": (
            "Dear Lan,\n\n"
            "I am very happy to invite you to my birthday party next Saturday at 6 p.m. at my house. "
            "You can come by bus because it is cheap and convenient. You can stay at my house for the night. "
            "We will eat cake, sing songs, take photos, and visit the night market together.\n\n"
            "Best regards,\nLinh"
        ),
    },
    {
        "name": "medium",
        "text": (
            "Dear Lan,\n\n"
            "I want to invite you to my birthday party next weekend. It is at my house. "
            "We will eat and play games together. Please come if you are free.\n\n"
            "Love,\nLinh"
        ),
    },
    {
        "name": "bad",
        "text": "Hello. I like music and food. My school is big. Thank you.",
    },
]

SPEAKING_CASES = [
    {
        "name": "tts_clean",
        "voice": "en-US-AriaNeural",
        "text": "My best friend is very kind and helpful. We study English and play badminton together every weekend.",
    },
    {
        "name": "tts_slow",
        "voice": "en-US-GuyNeural",
        "text": "I usually get up at six in the morning, have breakfast, and then go to school by bus.",
        "rate": "-35%",
    },
    {
        "name": "tts_low_volume",
        "voice": "en-US-AriaNeural",
        "text": "I enjoy learning English because it helps me communicate and travel with confidence.",
        "volume_filter": "volume=0.18",
    },
]


def require_login_credentials() -> Tuple[str, str]:
    if USER_EMAIL:
        return USER_EMAIL, USER_PASSWORD

    email = f"smoke_{int(time.time())}_{uuid.uuid4().hex[:6]}@example.com"
    return email, USER_PASSWORD


def show(title: str, response: requests.Response) -> Optional[dict]:
    print(f"\n=== {title} ===")
    print("STATUS", response.status_code)
    try:
        data = response.json()
        print(json.dumps(data, ensure_ascii=False)[:1400])
        return data
    except Exception:
        print(response.text[:1400])
        return None


def ensure_user(session: requests.Session, email: str, password: str) -> None:
    response = session.post(
        f"{BASE_URL}/auth/register",
        json={"email": email, "password": password, "full_name": "Smoke Test User"},
    )
    if response.status_code not in (201, 422):
        show("register", response)
        response.raise_for_status()


def login(session: requests.Session, email: str, password: str) -> None:
    response = session.post(f"{BASE_URL}/auth/login", json={"email": email, "password": password})
    data = show("login", response)
    response.raise_for_status()
    if data is None:
        raise RuntimeError("Login response was not JSON.")
    session.headers["Authorization"] = f"Bearer {data['data']['access_token']}"


def ensure_onboarding(session: requests.Session) -> None:
    status = session.get(f"{BASE_URL}/onboarding/status").json()["data"]
    if status["completed"]:
        return

    response = session.post(
        f"{BASE_URL}/onboarding/self-assess",
        json={
            "listening": "B1",
            "reading": "B1",
            "writing": "A2",
            "speaking": "A2",
            "target_band": "B1",
            "daily_study_time_minutes": 45,
            "deadline": "2026-12-31",
        },
    )
    show("onboarding/self-assess", response)
    response.raise_for_status()


def poll_submission(session: requests.Session, submission_id: str, max_polls: int = 15, delay: int = 3) -> dict:
    data = {}

    for _ in range(max_polls):
        response = session.get(f"{BASE_URL}/submissions/{submission_id}")
        data = response.json()["data"]
        if data["status"] in ("completed", "review_pending", "failed"):
            return data
        time.sleep(delay)

    return data


def start_writing(session: requests.Session) -> dict:
    response = session.post(
        f"{BASE_URL}/practice/sessions",
        json={"skill": "writing", "mode": "guided", "items_count": 1, "part": 1},
    )
    data = show("start writing practice", response)
    response.raise_for_status()
    if data is None:
        raise RuntimeError("Writing practice start response was not JSON.")
    return data["data"]


def run_writing_cases(session: requests.Session) -> list[dict]:
    results = []
    for case in WRITING_CASES:
        started = start_writing(session)
        session_id = started["session"]["id"]
        response = session.post(
            f"{BASE_URL}/practice/sessions/{session_id}/submit",
            json={"answer": {"text": case["text"]}},
        )
        data = show(f"submit writing {case['name']}", response)
        response.raise_for_status()
        if data is None:
            raise RuntimeError(f"Writing submit response was not JSON for case {case['name']}.")
        submission = poll_submission(session, data["data"]["submission_id"])
        results.append(
            {
                "case": case["name"],
                "status": submission["status"],
                "score": submission.get("score"),
                "band": submission.get("band"),
                "feedback": submission.get("feedback"),
            }
        )
    return results


def start_speaking(session: requests.Session) -> dict:
    response = session.post(
        f"{BASE_URL}/practice/sessions",
        json={"skill": "speaking", "mode": "shadowing", "items_count": 1},
    )
    data = show("start speaking practice", response)
    response.raise_for_status()
    if data is None:
        raise RuntimeError("Speaking practice start response was not JSON.")
    return data["data"]


def synthesize_audio(case: dict, out_dir: str) -> str:
    mp3 = str(Path(out_dir) / f"{case['name']}.mp3")
    wav = str(Path(out_dir) / f"{case['name']}.wav")

    command = [
        sys.executable,
        "-m",
        "edge_tts",
        "--voice",
        case["voice"],
        "--text",
        case["text"],
        "--write-media",
        mp3,
    ]

    if case.get("rate"):
        command.extend(["--rate", case["rate"]])

    subprocess.run(command, check=True)

    ffmpeg = ["ffmpeg", "-y", "-i", mp3]
    if case.get("volume_filter"):
        ffmpeg.extend(["-af", case["volume_filter"]])
    ffmpeg.extend(["-ar", "16000", "-ac", "1", wav])
    subprocess.run(ffmpeg, check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

    return wav


def upload_audio(session: requests.Session, file_path: str) -> str:
    file_size = os.path.getsize(file_path)
    response = session.post(
        f"{BASE_URL}/uploads/presign",
        json={"content_type": "audio/wav", "file_size": file_size},
    )
    data = show("presign upload", response)
    response.raise_for_status()
    if data is None:
        raise RuntimeError("Presign response was not JSON.")

    headers = data["data"].get("headers", {})
    if any(not isinstance(value, str) for value in headers.values()):
        raise RuntimeError(f"Presign headers must be strings: {headers!r}")

    with open(file_path, "rb") as file_handle:
        upload_response = requests.put(
            data["data"]["upload_url"],
            data=file_handle,
            headers={**headers, "Content-Type": "audio/wav"},
        )
    upload_response.raise_for_status()

    return data["data"]["audio_path"]


def run_speaking_cases(session: requests.Session) -> list[dict]:
    results = []
    with tempfile.TemporaryDirectory() as temp_dir:
        for case in SPEAKING_CASES:
            started = start_speaking(session)
            session_id = started["session"]["id"]
            wav = synthesize_audio(case, temp_dir)
            audio_path = upload_audio(session, wav)
            response = session.post(
                f"{BASE_URL}/practice/sessions/{session_id}/submit",
                json={"answer": {"audio_path": audio_path}},
            )
            data = show(f"submit speaking {case['name']}", response)
            response.raise_for_status()
            if data is None:
                raise RuntimeError(f"Speaking submit response was not JSON for case {case['name']}.")
            submission = poll_submission(session, data["data"]["submission_id"], max_polls=8, delay=2)
            pronunciation = submission.get("result", {}).get("pronunciation", {})
            results.append(
                {
                    "case": case["name"],
                    "status": submission["status"],
                    "score": submission.get("score"),
                    "accuracy": pronunciation.get("accuracy_score"),
                    "fluency": pronunciation.get("fluency_score"),
                    "prosody": pronunciation.get("prosody_score"),
                    "transcript": pronunciation.get("transcript"),
                }
            )
    return results


def print_summary(title: str, rows: list[dict], columns: list[str]) -> None:
    print(f"\n## {title}")
    widths = {column: max(len(column), *(len(str(row.get(column, ""))) for row in rows)) for column in columns}
    header = " | ".join(column.ljust(widths[column]) for column in columns)
    divider = "-+-".join("-" * widths[column] for column in columns)
    print(header)
    print(divider)
    for row in rows:
        print(" | ".join(str(row.get(column, "")).ljust(widths[column]) for column in columns))


def main() -> None:
    session = requests.Session()
    session.headers["Accept"] = "application/json"

    email, password = require_login_credentials()
    ensure_user(session, email, password)
    login(session, email, password)
    ensure_onboarding(session)

    writing_results = run_writing_cases(session)
    speaking_results = run_speaking_cases(session)

    print_summary("Writing smoke results", writing_results, ["case", "status", "score", "band"])
    print_summary("Speaking smoke results", speaking_results, ["case", "status", "score", "accuracy", "fluency", "prosody"])

    print("\nNotes:")
    for row in writing_results:
        print(f"- writing/{row['case']}: {str(row.get('feedback', ''))[:220]}")
    for row in speaking_results:
        print(f"- speaking/{row['case']}: {str(row.get('transcript', ''))[:220]}")


if __name__ == "__main__":
    main()
