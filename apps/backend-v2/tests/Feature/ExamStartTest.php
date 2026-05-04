<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Level;
use App\Enums\Skill;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ExamStartTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_session_detail_when_starting_an_exam(): void
    {
        $user = User::create([
            'full_name' => 'Exam Starter',
            'email' => 'exam-starter@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $token = JWTAuth::fromUser($user);

        $question = Question::create([
            'skill' => Skill::Writing,
            'level' => Level::B2,
            'part' => 1,
            'topic' => 'Writing',
            'content' => [
                'prompt' => 'Write a formal letter.',
                'minWords' => 150,
                'taskType' => 'letter',
                'instructions' => [],
                'requiredPoints' => ['purpose', 'activities', 'benefits'],
            ],
            'is_active' => true,
        ]);

        $exam = Exam::create([
            'title' => 'Writing Focus B2',
            'level' => Level::B2,
            'type' => 'practice',
            'duration_minutes' => 40,
            'blueprint' => [
                ['skill' => Skill::Writing->value, 'part' => 1, 'question_ids' => [$question->id]],
            ],
            'is_active' => true,
        ]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/exams/{$exam->id}/start")
            ->assertOk()
            ->assertJsonPath('data.session.exam_id', $exam->id)
            ->assertJsonPath('data.session.status', 'in_progress')
            ->assertJsonCount(1, 'data.questions')
            ->assertJsonPath('data.questions.0.id', $question->id);
    }
}
