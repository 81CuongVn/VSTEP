<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Level;
use App\Enums\Skill;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PracticeCatalogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_vstep_sections_grouped_by_skill_and_band(): void
    {
        $user = $this->makeLearner();

        Question::create([
            'skill' => Skill::Listening,
            'level' => Level::B1,
            'part' => 1,
            'topic' => 'Campus Announcements',
            'content' => ['audioUrl' => 'https://example.test/audio.mp3', 'items' => []],
            'is_active' => true,
        ]);

        Question::create([
            'skill' => Skill::Writing,
            'level' => Level::C1,
            'part' => 2,
            'topic' => 'Urban Development',
            'content' => ['prompt' => 'Discuss urban development', 'taskType' => 'essay', 'minWords' => 250],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/practice/catalog');

        $response
            ->assertOk()
            ->assertJsonPath('data.levels.0', 'B1')
            ->assertJsonPath('data.levels.1', 'B2')
            ->assertJsonPath('data.levels.2', 'C1')
            ->assertJsonPath('data.skills.0.key', 'listening')
            ->assertJsonPath('data.skills.0.parts.0.part', 1)
            ->assertJsonPath('data.skills.0.parts.0.bands.0.level', 'B1')
            ->assertJsonPath('data.skills.0.parts.0.bands.0.available', true)
            ->assertJsonPath('data.skills.0.parts.0.bands.0.question_count', 1)
            ->assertJsonPath('data.skills.0.parts.0.bands.0.topics.0', 'Campus Announcements')
            ->assertJsonPath('data.skills.2.key', 'writing')
            ->assertJsonPath('data.skills.2.supported_modes.1', 'guided')
            ->assertJsonPath('data.skills.2.parts.1.part', 2)
            ->assertJsonPath('data.skills.2.parts.1.bands.2.level', 'C1')
            ->assertJsonPath('data.skills.2.parts.1.bands.2.available', true)
            ->assertJsonPath('data.skills.2.parts.1.bands.2.question_count', 1)
            ->assertJsonPath('data.skills.2.parts.1.bands.2.topics.0', 'Urban Development');
    }

    private function makeLearner(): User
    {
        return User::create([
            'full_name' => 'Catalog Learner',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'learner',
        ]);
    }
}
