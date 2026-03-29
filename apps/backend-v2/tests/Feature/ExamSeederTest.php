<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ExamType;
use App\Enums\Level;
use App\Enums\Skill;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\ExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExamSeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_mock_exam_from_vstep_part_layout_instead_of_whole_bank(): void
    {
        User::create([
            'full_name' => 'Admin',
            'email' => 'admin@vstep.local',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->seedQuestions(Level::B1);

        app(ExamSeeder::class)->run();

        $exam = Exam::where('title', 'VSTEP Mock B1')->firstOrFail();

        $this->assertSame(ExamType::Mock, $exam->type);
        $this->assertSame(172, $exam->duration_minutes);
        $this->assertCount(12, $exam->blueprint);
        $this->assertSame([1, 2, 3], collect($exam->blueprint)->where('skill', 'listening')->pluck('part')->all());
        $this->assertSame([1, 2, 3, 4], collect($exam->blueprint)->where('skill', 'reading')->pluck('part')->all());
        $this->assertSame([1, 2], collect($exam->blueprint)->where('skill', 'writing')->pluck('part')->all());
        $this->assertSame([1, 2, 3], collect($exam->blueprint)->where('skill', 'speaking')->pluck('part')->all());
        $this->assertTrue(collect($exam->blueprint)->every(fn (array $section) => count($section['question_ids']) === 1));
    }

    #[Test]
    public function it_builds_practice_exam_with_only_listening_and_reading_parts(): void
    {
        User::create([
            'full_name' => 'Admin',
            'email' => 'admin@vstep.local',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->seedQuestions(Level::B1);

        app(ExamSeeder::class)->run();

        $exam = Exam::where('title', 'VSTEP Practice B1')->firstOrFail();

        $this->assertSame(ExamType::Practice, $exam->type);
        $this->assertSame(100, $exam->duration_minutes);
        $this->assertCount(7, $exam->blueprint);
        $this->assertSame(['listening', 'listening', 'listening', 'reading', 'reading', 'reading', 'reading'], collect($exam->blueprint)->pluck('skill')->all());
    }

    private function seedQuestions(Level $level): void
    {
        foreach ([1, 2, 3] as $part) {
            $this->makeQuestion(Skill::Listening, $level, $part);
            $this->makeQuestion(Skill::Speaking, $level, $part, false);
        }

        foreach ([1, 2, 3, 4] as $part) {
            $this->makeQuestion(Skill::Reading, $level, $part);
        }

        foreach ([1, 2] as $part) {
            $this->makeQuestion(Skill::Writing, $level, $part, false);
        }
    }

    private function makeQuestion(Skill $skill, Level $level, int $part, bool $objective = true): void
    {
        Question::create([
            'skill' => $skill,
            'level' => $level,
            'part' => $part,
            'topic' => "{$skill->value}-{$level->value}-{$part}",
            'content' => $objective
                ? ['items' => array_fill(0, 2, ['stem' => 'Test', 'options' => ['A', 'B', 'C', 'D']])]
                : ['prompt' => 'Test prompt'],
            'answer_key' => $objective ? ['correctAnswers' => ['1' => 'A', '2' => 'B']] : null,
            'is_active' => true,
        ]);
    }
}
