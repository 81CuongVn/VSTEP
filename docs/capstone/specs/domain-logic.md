# Domain Logic

## 1. Submission Lifecycle

### State Machine (5 states)

```
pending → processing → completed
                    → review_pending → completed
                    → failed
pending → failed
```

- **pending**: Created, not yet graded
- **processing**: L/R auto-grading in progress, or W/S dispatched to Redis queue
- **completed**: Final score available
- **review_pending**: AI graded but low/medium confidence → needs instructor review
- **failed**: Grading error after max retries

### Skill Routing

| Skill | Grading Method | Flow |
|-------|---------------|------|
| Listening | Auto-grade (answer key comparison) | Instant → completed |
| Reading | Auto-grade (answer key comparison) | Instant → completed |
| Writing | AI grading via LLM | Redis queue → Grading Worker → completed/review_pending |
| Speaking | STT + AI grading | Redis queue → Grading Worker → completed/review_pending |

### Grading Queue (Redis)

```
Backend: XADD "grading:tasks" { submissionId, questionId, skill, answer, dispatchedAt }
Worker:  XREADGROUP "grading:tasks" → grade via LiteLLM → XADD "grading:results" { submissionId, score, ... }
Backend: consumes "grading:results" → UPDATE submissions/submission_details in PostgreSQL
```

Worker has **no direct DB access**. Results flow back via Redis Streams.
Worker failure: max 3 retries with exponential backoff. After exhaustion → failure marker to results stream.

## 2. Grading Pipeline

### AI Grading (Writing)

1. Worker receives task from Redis Streams (XREADGROUP)
2. Call LLM via LiteLLM (provider-agnostic: default Groq Llama 3.3 70B, fallback Cloudflare)
3. Parse structured response: criteria scores (Task Achievement, Coherence, Lexical Resource, Grammar)
4. Calculate overall score (weighted average)
5. Determine confidence: high (≥85%) / medium (70-84%) / low (<70%)
6. XADD result to `grading:results` stream

### AI Grading (Speaking)

1. Worker receives task from Redis Streams
2. Download audio → Transcribe via LiteLLM STT (default Groq Whisper V3 Turbo, fallback Cloudflare Deepgram Nova 3)
3. STT results cached in Redis
4. Grade transcript via LiteLLM LLM with Speaking rubric
5. Parse criteria scores (Pronunciation, Fluency, Lexical, Grammar, Interaction)
6. XADD result to `grading:results` stream

### Confidence Routing

| Confidence | Action |
|-----------|--------|
| High (≥85%) | → `completed` |
| Medium (70-84%) | → `review_pending` (priority: medium) |
| Low (<70%) | → `review_pending` (priority: high) |

### Human Review Merge Rules

When instructor submits review:
- `scoreDiff = abs(ai.score - human.score)`
- If `scoreDiff ≤ 0.5`: `final = ai × 0.4 + human × 0.6`, gradingMode = `hybrid`
- Else: `final = human`, gradingMode = `human`, auditFlag = true

## 3. Progress Tracking

### Sliding Window

Per skill, last 10 completed submissions:
- `windowAvg`: average score
- `windowStdDev`: standard deviation
- Minimum 3 attempts required; else `insufficient_data`

### Trend Classification

`delta = avg(last 3) - avg(previous 3)`

| Condition | Trend |
|-----------|-------|
| `stdDev ≥ 1.5` | inconsistent |
| `delta ≥ +0.5` | improving |
| `delta ≤ -0.5` | declining |
| else | stable |

### Overall Band

- Per-skill band from latest grading result
- `overallBand = min(4 skill bands)`
- Missing data → `low_confidence`

### ETA Heuristic

- Requires goal (targetBand) + ≥6 attempts per skill
- `rate` = weekly change in windowAvg
- `totalETA = max(ETA per skill)` — slowest skill determines

## 4. Adaptive Scaffolding

### Stages

| Stage | Level | Support |
|-------|-------|---------|
| 1 | Template | Full template + guided prompts |
| 2 | Keywords | Keywords + partial hints |
| 3 | Free | No support |

### Progression Rules (Writing, scorePct = score × 10)

| Current | Level Up | Level Down |
|---------|----------|------------|
| Stage 1 | avg3 ≥ 80 → Stage 2 | — |
| Stage 2 | avg3 ≥ 75 → Stage 3 | avg3 < 60 (2 consecutive) → Stage 1 |
| Stage 3 | — | avg3 < 65 (2 consecutive) → Stage 2 |

### Initial Assignment

| Placement Level | Starting Stage |
|----------------|---------------|
| A2 or below | Stage 1 |
| B1 | Stage 2 |
| B2–C1 | Stage 3 |

## 5. SSE (Real-time Notifications)

### Endpoint

```
GET /api/sse/submissions/:id?token=<jwt>
```

Auth via query param (EventSource doesn't support custom headers).

### Events

| Event | When |
|-------|------|
| `grading.progress` | Grading in progress (step, percent) |
| `grading.completed` | Final result ready (score, band) |
| `grading.review_pending` | Needs human review |
| `grading.failed` | Grading failed |
| `ping` | Heartbeat every 30s |

### Implementation

- Elysia built-in SSE + async generator
- Redis pub/sub channel per submission for cross-process broadcast
- Heartbeat: 30s ping
- Max lifetime: 30 min
- Auto-close on terminal state (completed/failed) + 5s grace

## 6. Exam Session Flow

1. Learner starts session → status: `in_progress`
2. Client auto-saves answers every 30s
3. Learner submits → L/R auto-graded instantly, W/S dispatched to queue
4. Session status: `submitted`
5. When all 4 skills scored → `completed`, overall score = avg(4 skills)
6. Timeout without submit → `abandoned`

## 7. Authentication

- Access token: 15 min, HS256 JWT (`sub`, `role`, `iat`, `exp`)
- Refresh token: 7 days, stored as SHA-256 hash
- Max 3 active refresh tokens per user (FIFO)
- Reuse detection: rotated token reused → revoke ALL user tokens
- Password: Argon2id via `Bun.password`

---

*Reflects implemented logic as of March 2026.*
