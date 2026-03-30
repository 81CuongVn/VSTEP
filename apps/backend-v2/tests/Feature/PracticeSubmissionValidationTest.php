<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Level;
use App\Enums\PracticeMode;
use App\Enums\Skill;
use App\Models\PracticeSession;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PracticeSubmissionValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_rejects_blob_audio_urls_for_speaking_submissions(): void
    {
        [$user, $session] = $this->makeSpeakingSession();

        $this->actingAs($user, 'api')
            ->postJson("/api/v1/practice/sessions/{$session->id}/submit", [
                'answer' => [
                    'audio_path' => 'blob:http://localhost:5173/demo',
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['answer.audio_path']);
    }

    #[Test]
    public function it_requires_uploaded_storage_paths_for_speaking_submissions(): void
    {
        [$user, $session] = $this->makeSpeakingSession();

        $this->actingAs($user, 'api')
            ->postJson("/api/v1/practice/sessions/{$session->id}/submit", [
                'answer' => [
                    'audio_path' => 'tmp/local-file.webm',
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['answer.audio_path']);
    }

    private function makeSpeakingSession(): array
    {
        $user = User::create([
            'full_name' => 'Speaker',
            'email' => 'speaker@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $question = Question::create([
            'skill' => Skill::Speaking,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Speaking validation',
            'content' => ['prompt' => 'Talk about your hobby'],
            'is_active' => true,
        ]);

        $session = PracticeSession::create([
            'user_id' => $user->id,
            'skill' => Skill::Speaking,
            'mode' => PracticeMode::Drill,
            'level' => Level::B1,
            'current_question_id' => $question->id,
            'config' => ['items_count' => 1],
            'started_at' => now(),
        ]);

        return [$user, $session];
    }
}
