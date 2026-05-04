<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\KnowledgePointCategory;
use App\Enums\Level;
use App\Enums\Skill;
use App\Models\KnowledgePoint;
use App\Models\Question;
use App\Models\User;
use App\Models\UserWeakPoint;
use App\Services\QuestionPicker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PracticeReviewPickerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_marks_due_weak_point_questions_as_review_items(): void
    {
        $user = User::create([
            'full_name' => 'Review Learner',
            'email' => 'review-learner@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $knowledgePoint = KnowledgePoint::create([
            'category' => KnowledgePointCategory::Grammar,
            'name' => 'Articles',
            'description' => 'Article usage',
        ]);

        $reviewQuestion = Question::create([
            'skill' => Skill::Reading,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Review topic',
            'content' => [
                'title' => 'Review passage',
                'passage' => 'A short passage.',
                'items' => [
                    ['stem' => 'Question 1', 'options' => ['A', 'B']],
                ],
            ],
            'answer_key' => ['correctAnswers' => ['1' => 'A']],
            'is_active' => true,
        ]);
        $reviewQuestion->knowledgePoints()->attach($knowledgePoint->id);

        UserWeakPoint::create([
            'user_id' => $user->id,
            'knowledge_point_id' => $knowledgePoint->id,
            'skill' => Skill::Reading,
            'next_review_at' => now()->subMinute(),
            'is_mastered' => false,
        ]);

        $pick = app(QuestionPicker::class)->pick(
            $user->id,
            Skill::Reading,
            Level::B1,
            3,
            8,
            collect(),
        );

        $this->assertTrue($pick['is_review']);
        $this->assertNotNull($pick['question']);
        $this->assertSame($reviewQuestion->id, $pick['question']?->id);
    }
}
