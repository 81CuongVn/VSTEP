<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Level;
use App\Enums\PracticeMode;
use App\Enums\Skill;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PracticeStartConstraintsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_starts_writing_practice_with_topic_constraint(): void
    {
        $user = $this->makeLearner();

        $target = Question::create([
            'skill' => Skill::Writing,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Library Facilities',
            'content' => ['prompt' => 'Write about the library', 'taskType' => 'letter'],
            'is_active' => true,
        ]);

        Question::create([
            'skill' => Skill::Writing,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Smartphones in Class',
            'content' => ['prompt' => 'Write about smartphones', 'taskType' => 'letter'],
            'is_active' => true,
        ]);

        $this->actingAs($user, 'api')
            ->postJson('/api/v1/practice/sessions', [
                'skill' => Skill::Writing->value,
                'mode' => PracticeMode::Guided->value,
                'level' => Level::B1->value,
                'items_count' => 1,
                'topic' => 'Library Facilities',
            ])
            ->assertCreated()
            ->assertJsonPath('data.current_item.question.id', $target->id)
            ->assertJsonPath('data.current_item.question.topic', 'Library Facilities')
            ->assertJsonPath('data.session.config.topic', 'Library Facilities');
    }

    #[Test]
    public function it_starts_writing_practice_with_part_constraint(): void
    {
        $user = $this->makeLearner();

        Question::create([
            'skill' => Skill::Writing,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Letter topic',
            'content' => ['prompt' => 'Write a letter', 'taskType' => 'letter'],
            'is_active' => true,
        ]);

        $target = Question::create([
            'skill' => Skill::Writing,
            'level' => Level::B1,
            'part' => 2,
            'topic' => 'Essay topic',
            'content' => ['prompt' => 'Write an essay', 'taskType' => 'essay'],
            'is_active' => true,
        ]);

        $this->actingAs($user, 'api')
            ->postJson('/api/v1/practice/sessions', [
                'skill' => Skill::Writing->value,
                'mode' => PracticeMode::Guided->value,
                'level' => Level::B1->value,
                'items_count' => 1,
                'part' => 2,
            ])
            ->assertCreated()
            ->assertJsonPath('data.current_item.question.id', $target->id)
            ->assertJsonPath('data.current_item.question.part', 2)
            ->assertJsonPath('data.session.config.part', 2);
    }

    private function makeLearner(): User
    {
        return User::create([
            'full_name' => 'Writer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'learner',
        ]);
    }
}
