<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Level;
use App\Enums\PracticeMode;
use App\Enums\Skill;
use App\Enums\SubmissionStatus;
use App\Jobs\GradeSubmission;
use App\Models\PracticeSession;
use App\Models\Question;
use App\Models\Submission;
use App\Models\UserProgress;
use App\Support\VstepScoring;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PracticeService
{
    private const RECENT_EXCLUDE_LIMIT = 50;

    private const REVIEW_MIX_RATIO = 0.3; // 30% review items in session

    public function __construct(
        private readonly WeakPointService $weakPointService,
        private readonly PronunciationService $pronunciationService,
    ) {}

    // ── Session lifecycle ──────────────────────────────────────

    public function start(string $userId, Skill $skill, PracticeMode $mode, array $options = []): array
    {
        if (! $mode->availableForSkill($skill)) {
            throw ValidationException::withMessages([
                'mode' => ["Mode {$mode->value} is not available for {$skill->value}."],
            ]);
        }

        $progress = UserProgress::findOrInitialize($userId, $skill);
        $level = $options['level'] ?? $progress->current_level;
        $itemsCount = $options['items_count'] ?? $mode->defaultItemsCount();

        $session = PracticeSession::create([
            'user_id' => $userId,
            'skill' => $skill,
            'mode' => $mode,
            'level' => $level,
            'config' => [
                'items_count' => $itemsCount,
                'focus_kp' => $options['focus_kp'] ?? null,
            ],
            'progress' => ['current_index' => 0, 'items' => []],
            'started_at' => now(),
        ]);

        $recommendation = $this->weakPointService->getRecommendation($userId, $skill);
        $firstItem = $this->buildNextItem($session, $userId);

        return [
            'session' => $session,
            'current_item' => $firstItem,
            'recommendation' => $recommendation,
        ];
    }

    public function submitItem(PracticeSession $session, array $answer): array
    {
        if ($session->isCompleted()) {
            throw ValidationException::withMessages([
                'session' => ['Session is already completed.'],
            ]);
        }

        $currentIndex = $session->currentIndex();
        $items = $session->items();
        $currentItem = $items[$currentIndex] ?? null;

        if (! $currentItem) {
            throw ValidationException::withMessages([
                'session' => ['No current item to submit.'],
            ]);
        }

        $question = Question::findOrFail($currentItem['question_id']);
        $result = $this->processAnswer($session, $question, $answer);

        // Check if this is a retry
        $isRetry = ! empty($currentItem['attempts']);
        $attempt = [
            'answer' => $answer,
            'result' => $result,
            'submitted_at' => now()->toAtomString(),
        ];

        // Update item with attempt
        $items[$currentIndex]['attempts'][] = $attempt;
        $items[$currentIndex]['best_score'] = max(
            $items[$currentIndex]['best_score'] ?? 0,
            $result['score'] ?? 0,
        );

        // Create submission for adaptive tracking (only first attempt)
        $submissionId = $currentItem['submission_id'] ?? null;
        if (! $isRetry && $session->mode !== PracticeMode::Drill) {
            $submission = Submission::create([
                'user_id' => $session->user_id,
                'question_id' => $question->id,
                'skill' => $session->skill,
                'answer' => $answer,
                'status' => SubmissionStatus::Pending,
            ]);
            $items[$currentIndex]['submission_id'] = $submission->id;
            $submissionId = $submission->id;

            if (! $question->skill->isObjective()) {
                GradeSubmission::dispatch($submission->id);
            } else {
                $this->autoGradeObjective($submission, $question);
            }
        }

        // Advance to next item (unless retry)
        $nextItem = null;
        if (! $isRetry) {
            $progress = $session->progress;
            $progress['current_index'] = $currentIndex + 1;
            $progress['items'] = $items;
            $session->update(['progress' => $progress]);

            if ($session->hasMoreItems()) {
                $nextItem = $this->buildNextItem($session, $session->user_id);
            }
        } else {
            $progress = $session->progress;
            $progress['items'] = $items;
            $session->update(['progress' => $progress]);
        }

        return [
            'result' => $result,
            'attempt_number' => count($items[$currentIndex]['attempts']),
            'next_item' => $nextItem,
            'progress' => [
                'current' => $currentIndex + ($isRetry ? 0 : 1),
                'total' => $session->totalItems(),
                'has_more' => $session->fresh()->hasMoreItems(),
            ],
            'submission_id' => $submissionId,
        ];
    }

    public function retryItem(PracticeSession $session, array $answer): array
    {
        return $this->submitItem($session, $answer);
    }

    public function complete(PracticeSession $session): PracticeSession
    {
        if ($session->isCompleted()) {
            return $session;
        }

        $items = $session->items();
        $scores = collect($items)->pluck('best_score')->filter();

        $summary = [
            'items_completed' => count($items),
            'items_total' => $session->totalItems(),
            'average_score' => $scores->isNotEmpty() ? VstepScoring::round($scores->avg()) : null,
            'best_score' => $scores->isNotEmpty() ? $scores->max() : null,
            'completed_at' => now()->toAtomString(),
        ];

        // Compare with last session of same type
        $lastSession = PracticeSession::forUser($session->user_id)
            ->where('skill', $session->skill)
            ->where('mode', $session->mode)
            ->completed()
            ->orderByDesc('completed_at')
            ->first();

        if ($lastSession?->summary) {
            $lastAvg = $lastSession->summary['average_score'] ?? 0;
            $currentAvg = $summary['average_score'] ?? 0;
            $summary['improvement'] = $currentAvg - $lastAvg;
        }

        $session->update([
            'summary' => $summary,
            'completed_at' => now(),
        ]);

        return $session;
    }

    public function list(string $userId, ?Skill $skill = null): LengthAwarePaginator
    {
        return PracticeSession::forUser($userId)
            ->when($skill, fn ($q, $v) => $q->where('skill', $v))
            ->orderByDesc('started_at')
            ->paginate();
    }

    // ── Item building ──────────────────────────────────────────

    private function buildNextItem(PracticeSession $session, string $userId): ?array
    {
        if (! $session->hasMoreItems()) {
            return null;
        }

        $index = $session->currentIndex();
        $total = $session->totalItems();
        $difficulty = $this->resolveDifficulty($session, $index, $total);

        // Mix review items (30% of session)
        $reviewItem = null;
        if ($index > 0 && ($index % 3 === 0)) {
            $reviewItem = $this->getReviewItem($userId, $session->skill);
        }

        $question = $reviewItem ?? $this->findQuestion(
            $session->skill,
            $difficulty,
            $session->config['focus_kp'] ?? null,
            $this->getExcludeIds($session, $userId),
            $session->mode,
        );

        if (! $question) {
            return null;
        }

        $question->makeHidden(['answer_key', 'explanation']);

        $item = [
            'index' => $index,
            'question_id' => $question->id,
            'question' => $question->toArray(),
            'difficulty' => $difficulty->value,
            'is_review' => $reviewItem !== null,
            'attempts' => [],
            'best_score' => null,
        ];

        // Add mode-specific content
        if ($session->mode === PracticeMode::Shadowing) {
            $item['reference_text'] = $question->content['prompt'] ?? '';
            $item['reference_audio_url'] = $this->getOrGenerateReferenceAudio($question);
        }

        // Save item to progress
        $progress = $session->progress;
        $progress['items'][] = $item;
        $session->update(['progress' => $progress]);

        return $item;
    }

    private function resolveDifficulty(PracticeSession $session, int $index, int $total): Level
    {
        $baseLevel = $session->level;
        $ratio = $total > 1 ? $index / ($total - 1) : 0;

        return match (true) {
            $ratio < 0.3 => $baseLevel->prev() ?? $baseLevel,  // Warm-up
            $ratio > 0.7 => $baseLevel->next() ?? $baseLevel,  // Stretch
            default => $baseLevel,                               // Challenge
        };
    }

    private function getReviewItem(string $userId, Skill $skill): ?Question
    {
        $dueWp = $this->weakPointService->getDueForReview($userId, $skill, 1)->first();

        if (! $dueWp) {
            return null;
        }

        return Question::where('skill', $skill)
            ->where('is_active', true)
            ->whereHas('knowledgePoints', fn ($q) => $q->where('knowledge_points.id', $dueWp->knowledge_point_id))
            ->inRandomOrder()
            ->first();
    }

    private function findQuestion(Skill $skill, Level $level, ?string $focusKp, Collection $excludeIds, PracticeMode $mode): ?Question
    {
        $query = Question::where('skill', $skill)
            ->where('level', $level)
            ->where('is_active', true)
            ->when($excludeIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $excludeIds));

        if ($focusKp) {
            $query->whereHas('knowledgePoints', fn ($q) => $q->where('name', $focusKp));
        }

        // For speaking modes, only get questions with prompts
        if (in_array($mode, [PracticeMode::Shadowing, PracticeMode::Drill])) {
            $query->whereNotNull('topic');
        }

        return $query->inRandomOrder()->first()
            ?? Question::where('skill', $skill)->where('level', $level)->where('is_active', true)->inRandomOrder()->first();
    }

    private function getExcludeIds(PracticeSession $session, string $userId): Collection
    {
        $sessionQuestionIds = collect($session->items())->pluck('question_id');

        $recentIds = Submission::forUser($userId)
            ->where('skill', $session->skill)
            ->orderByDesc('created_at')
            ->limit(self::RECENT_EXCLUDE_LIMIT)
            ->pluck('question_id');

        return $sessionQuestionIds->merge($recentIds)->unique();
    }

    // ── Answer processing ──────────────────────────────────────

    private function processAnswer(PracticeSession $session, Question $question, array $answer): array
    {
        return match ($session->mode) {
            PracticeMode::Shadowing => $this->processShadowing($question, $answer),
            PracticeMode::Drill => $this->processDrill($answer),
            default => $this->processFree($question, $answer),
        };
    }

    private function processFree(Question $question, array $answer): array
    {
        if ($question->skill->isObjective()) {
            $result = $question->gradeObjective($answer['answers'] ?? []);

            return [
                'type' => 'objective',
                'correct' => $result['all_correct'] ?? false,
                'score' => $result ? VstepScoring::score($result['raw_ratio']) : 0,
                'correct_answers' => $question->answer_key,
            ];
        }

        // Subjective — graded async by GradeSubmission job
        return ['type' => 'subjective', 'status' => 'processing'];
    }

    private function processShadowing(Question $question, array $answer): array
    {
        $referenceText = $question->content['prompt'] ?? '';
        $audioPath = $answer['audio_path'] ?? '';

        if (empty($audioPath)) {
            return ['type' => 'shadowing', 'error' => 'No audio provided'];
        }

        $pronunciation = $this->pronunciationService->assessPronunciation($audioPath);

        return [
            'type' => 'shadowing',
            'score' => VstepScoring::round($pronunciation['accuracy_score'] / 10),
            'pronunciation' => $pronunciation,
            'reference_text' => $referenceText,
        ];
    }

    private function processDrill(array $answer): array
    {
        $audioPath = $answer['audio_path'] ?? '';

        if (empty($audioPath)) {
            return ['type' => 'drill', 'error' => 'No audio provided'];
        }

        $pronunciation = $this->pronunciationService->assessPronunciation($audioPath);

        return [
            'type' => 'drill',
            'score' => VstepScoring::round($pronunciation['accuracy_score'] / 10),
            'pronunciation' => $pronunciation,
        ];
    }

    private function autoGradeObjective(Submission $submission, Question $question): void
    {
        $result = $question->gradeObjective($submission->answer['answers'] ?? []);
        if (! $result) {
            return;
        }

        $score = VstepScoring::score($result['raw_ratio']);
        $submission->update([
            'status' => SubmissionStatus::Completed,
            'score' => $score,
            'result' => ['type' => 'auto', 'correct_count' => $result['correct'], 'total_count' => $result['total']],
            'completed_at' => now(),
        ]);
    }

    // ── Reference audio ────────────────────────────────────────

    private function getOrGenerateReferenceAudio(Question $question): ?string
    {
        // For speaking questions, reference audio could be pre-generated or on-demand
        // Return null for now — FE can use Web Speech API or we generate via edge-tts
        return null;
    }
}
