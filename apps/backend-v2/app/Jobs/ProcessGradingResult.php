<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Enums\SubmissionStatus;
use App\Enums\VstepBand;
use App\Models\Submission;
use App\Services\NotificationService;
use App\Services\ProgressService;
use App\Support\VstepScoring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Backoff([5, 15, 60])]
class ProcessGradingResult implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        private readonly string $submissionId,
    ) {}

    public function handle(ProgressService $progressService, NotificationService $notificationService): void
    {
        $submission = Submission::findOrFail($this->submissionId);
        $submission->loadMissing('question');

        $response = Http::timeout(90)
            ->post(config('services.grading.url').'/grade', [
                'submissionId' => $submission->id,
                'questionId' => $submission->question_id,
                'skill' => $submission->skill->value,
                'answer' => $submission->answer,
                'dispatchedAt' => now()->toAtomString(),
            ]);

        if ($response->failed()) {
            Log::error('grading_http_failed', [
                'submission_id' => $submission->id,
                'status' => $response->status(),
            ]);
            throw new \RuntimeException("Grading service returned {$response->status()}");
        }

        $data = $response->json();
        $score = VstepScoring::round($data['overallScore']);
        $confidence = $data['confidence'] ?? 'medium';

        $status = $confidence === 'low'
            ? SubmissionStatus::ReviewPending
            : SubmissionStatus::Completed;

        $submission->update([
            'status' => $status,
            'score' => $score,
            'band' => VstepBand::fromScore($score),
            'result' => $data,
            'feedback' => $data['feedback'] ?? null,
            'completed_at' => now(),
        ]);

        if ($status === SubmissionStatus::Completed) {
            $progressService->applySubmission($submission);
        }

        $notificationService->send(
            $submission->user_id,
            NotificationType::GradingComplete,
            'Bài làm đã được chấm điểm',
            "Bạn đạt {$score}/10 cho bài {$submission->skill->value}.",
            ['submission_id' => $submission->id, 'score' => $score],
        );

        Log::info('grading_completed', ['submission_id' => $submission->id, 'score' => $score]);
    }
}
