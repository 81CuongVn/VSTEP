# API Contracts

All endpoints under `/api`. Auth via JWT Bearer token (`Authorization: Bearer <token>`).

## Conventions

- Pagination: `?page=1&limit=20` → response `meta: { total, page, limit, totalPages }`
- Response wrapper: `{ data, meta? }`
- Error wrapper: `{ error: { code, message, requestId } }`
- Dates: ISO 8601 UTC
- IDs: UUID v7

## Endpoints

### Auth (`/api/auth`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | /register | No | Create account (email, password, fullName) → user info |
| POST | /login | No | Email/password → access + refresh tokens |
| POST | /refresh | Refresh token | Rotate refresh token → new token pair |
| POST | /logout | Yes | Revoke refresh token |
| GET | /me | Yes | Current user profile |

- Max 3 active refresh tokens per user (FIFO eviction)
- Reuse detection: rotated token reused → revoke all user tokens

### Submissions (`/api/submissions`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | / | Learner+ | Submit answer. L/R: auto-grade instant. W/S: dispatch to Redis queue |
| GET | / | Learner+ | List own submissions. Filter: skill, status |
| GET | /:id | Owner | Submission detail with result |

### Questions (`/api/questions`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | / | Yes | List questions. Filter: skill, level |
| GET | /:id | Yes | Question detail |
| POST | / | Admin | Create question |
| PUT | /:id | Admin | Update question |
| DELETE | /:id | Admin | Hard delete |

### Progress (`/api/progress`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | /overview | Learner+ | 4-skill summary: level, score, trend, scaffold |
| GET | /skills/:skill | Learner+ | Skill detail: sliding window scores, trend |
| GET | /activity | Learner+ | Activity heatmap data |
| GET | /history | Learner+ | Score history for charts |

### Goals (`/api/progress/goals`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | / | Learner+ | Current goal |
| POST | / | Learner+ | Create goal (targetBand, deadline?) |
| PATCH | /:id | Owner | Update goal |

### Exams (`/api/exams`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | / | Yes | List exams. Filter: type, skill |
| GET | /:id | Yes | Exam detail (sections, questions, time) |
| POST | / | Admin | Create exam |
| POST | /sessions | Learner+ | Start exam session |
| GET | /sessions/:id | Owner | Session status + answers |
| PUT | /sessions/:id/answers | Owner | Save answers (auto-save) |
| POST | /sessions/:id/submit | Owner | Submit exam. Auto-grade L/R, dispatch W/S |

### Classes (`/api/classes`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | / | Yes | List classes |
| POST | / | Instructor+ | Create class |
| POST | /join | Learner+ | Join class by invite code |
| GET | /:id | Member | Class detail |
| GET | /:id/dashboard | Instructor+ | Class dashboard (averages, at-risk) |
| GET | /:id/members | Instructor+ | Member list with progress |
| POST | /:id/feedback | Instructor+ | Post feedback to learner |
| DELETE | /:id/members/:userId | Instructor+ | Remove member |

### Vocabulary (`/api/vocabulary`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | /topics | Yes | List vocabulary topics |
| GET | /topics/:id/words | Yes | Words in topic |
| POST | /words/:id/progress | Learner+ | Mark word as known/unknown |

### Notifications (`/api/notifications`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | / | Yes | List notifications |
| PATCH | /:id/read | Owner | Mark as read |
| POST | /device-tokens | Yes | Register push token |

### Admin (`/api/admin`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | /users | Admin | List users |
| PATCH | /users/:id/role | Admin | Change user role |
| GET | /submissions/queue | Instructor+ | Review queue (review_pending) |
| POST | /submissions/:id/claim | Instructor+ | Claim for review (Redis lock 15min) |
| POST | /submissions/:id/release | Instructor+ | Release claim |
| PUT | /submissions/:id/review | Instructor+ | Submit human review |

### Other

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | /health | No | Health check (DB + Redis) |
| POST | /uploads/audio | Yes | Upload audio file to MinIO |
| GET | /sse/submissions/:id | Query token | SSE stream for grading status |

## Error Codes

| HTTP | Code | Description |
|------|------|-------------|
| 400 | VALIDATION_ERROR | Invalid input |
| 401 | UNAUTHORIZED | Missing/invalid token |
| 401 | TOKEN_EXPIRED | Access token expired |
| 403 | FORBIDDEN | Insufficient role or not owner |
| 404 | NOT_FOUND | Resource not found |
| 409 | CONFLICT | Duplicate or invalid state transition |
| 500 | INTERNAL_ERROR | Unexpected server error |

---

*Reflects implemented API as of March 2026.*
