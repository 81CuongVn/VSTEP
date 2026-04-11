<?php

declare(strict_types=1);

namespace App\Services\PracticeHandlers;

use App\Enums\SubmissionStatus;
use App\Jobs\GradeSubmission;
use App\Models\Question;
use App\Models\Submission;
use App\Support\VstepScoring;

class FreeModeHandler implements PracticeModeHandler
{
    public function processAnswer(Submission $submission, Question $question, array $answer): array
    {
        if ($question->skill->isObjective()) {
            return $this->gradeObjective($submission, $question, $answer);
        }

        GradeSubmission::dispatch($submission->id);

        return ['type' => 'subjective', 'status' => 'processing'];
    }

    public function supportsRetry(): bool
    {
        return false;
    }

    public function enrichItem(Question $question, ?int $writingTier = null): array
    {
        return [];
    }

    private function gradeObjective(Submission $submission, Question $question, array $answer): array
    {
        $result = $question->gradeObjective($answer['answers'] ?? []) ?? [
            'correct' => 0,
            'total' => 0,
            'raw_ratio' => 0.0,
            'all_correct' => false,
            'user_answers' => [],
            'correct_answers' => [],
            'items' => [],
        ];
        $score = VstepScoring::score($result['raw_ratio']);

        $submission->update([
            'status' => SubmissionStatus::Completed,
            'score' => $score,
            'result' => ['type' => 'objective', ...$result],
            'completed_at' => now(),
        ]);

        return [
            'type' => 'objective',
            ...$result,
            'correct' => $result['all_correct'],
            'score' => $score,
        ];
    }
}
