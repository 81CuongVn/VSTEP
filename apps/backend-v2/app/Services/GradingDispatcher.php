<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\Support\Facades\Redis;

class GradingDispatcher
{
    private const STREAM = 'grading:tasks';

    public function dispatch(Submission $submission): void
    {
        if ($submission->skill->isObjective()) {
            return;
        }

        $submission->update(['status' => SubmissionStatus::Processing]);

        $answer = $submission->answer;

        if (isset($answer['audioUrl'])) {
            $answer['audioUrl'] = $this->toInternalUrl($answer['audioUrl']);
        }

        $payload = json_encode([
            'submissionId' => $submission->id,
            'questionId' => $submission->question_id,
            'skill' => $submission->skill->value,
            'answer' => $answer,
            'dispatchedAt' => now()->toAtomString(),
        ], JSON_THROW_ON_ERROR);

        Redis::xadd(self::STREAM, '*', ['payload' => $payload]);
    }

    private function toInternalUrl(string $url): string
    {
        $appUrl = config('app.url', 'http://localhost');
        $internalUrl = config('services.grading.internal_backend_url', $appUrl);

        return str_replace($appUrl, $internalUrl, $url);
    }
}
