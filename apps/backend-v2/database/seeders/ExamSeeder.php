<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ExamType;
use App\Enums\Level;
use App\Enums\Skill;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    private const LISTENING_PARTS = [1, 2, 3];

    private const READING_PARTS = [1, 2, 3, 4];

    private const WRITING_PARTS = [1, 2];

    private const SPEAKING_PARTS = [1, 2, 3];

    public function run(): void
    {
        $admin = User::where('email', 'admin@vstep.local')->first();

        // Group question IDs by skill -> level -> part, so exams follow the real VSTEP structure.
        $bySkillLevel = [];
        foreach (Skill::cases() as $skill) {
            $bySkillLevel[$skill->value] = Question::where('skill', $skill)
                ->where('is_active', true)
                ->get()
                ->groupBy(fn ($q) => $q->level->value)
                ->map(fn (Collection $qs) => $qs->groupBy('part')->map(fn (Collection $partQs) => $partQs->pluck('id')->values()->all()));
        }

        // Practice exams per level: Listening + Reading with the real part layout.

        foreach (Level::cases() as $level) {
            $sections = [
                ...$this->buildSections($bySkillLevel, Skill::Listening, $level, self::LISTENING_PARTS),
                ...$this->buildSections($bySkillLevel, Skill::Reading, $level, self::READING_PARTS),
            ];

            if (count($sections) !== count(self::LISTENING_PARTS) + count(self::READING_PARTS)) {
                continue;
            }

            Exam::updateOrCreate(
                ['title' => "VSTEP Practice {$level->value}", 'type' => ExamType::Practice],
                [
                    'level' => $level,
                    'duration_minutes' => 100,
                    'blueprint' => $sections,
                    'description' => "Đề luyện tập VSTEP trình độ {$level->value} — Listening & Reading",
                    'is_active' => true,
                    'created_by' => $admin?->id,
                ],
            );
        }

        // Mock exams per level: full 4 skills using the exact VSTEP part layout.

        foreach ([Level::B1, Level::B2] as $level) {
            $sections = [
                ...$this->buildSections($bySkillLevel, Skill::Listening, $level, self::LISTENING_PARTS),
                ...$this->buildSections($bySkillLevel, Skill::Reading, $level, self::READING_PARTS),
                ...$this->buildSections($bySkillLevel, Skill::Writing, $level, self::WRITING_PARTS),
                ...$this->buildSections($bySkillLevel, Skill::Speaking, $level, self::SPEAKING_PARTS),
            ];

            if (count($sections) !== 12) {
                continue;
            }

            Exam::updateOrCreate(
                ['title' => "VSTEP Mock {$level->value}", 'type' => ExamType::Mock],
                [
                    'level' => $level,
                    'duration_minutes' => 172,
                    'blueprint' => $sections,
                    'description' => "Đề thi thử VSTEP trình độ {$level->value} — 4 kỹ năng",
                    'is_active' => true,
                    'created_by' => $admin?->id,
                ],
            );
        }

        // Placement exam: still limited to listening + reading, but keep the real part layout.

        $placementSections = [
            ...$this->buildPlacementSections($bySkillLevel, Skill::Listening, self::LISTENING_PARTS),
            ...$this->buildPlacementSections($bySkillLevel, Skill::Reading, self::READING_PARTS),
        ];

        if (count($placementSections) === count(self::LISTENING_PARTS) + count(self::READING_PARTS)) {
            Exam::updateOrCreate(
                ['title' => 'VSTEP Placement Test', 'type' => ExamType::Placement],
                [
                    'level' => Level::B1,
                    'duration_minutes' => 100,
                    'blueprint' => $placementSections,
                    'description' => 'Bài test xếp lớp — đánh giá trình độ Listening & Reading',
                    'is_active' => true,
                    'created_by' => $admin?->id,
                ],
            );
        }
    }

    /**
     * @param array<string, Collection<string, Collection<int, array<int, string>>>> $bySkillLevel
     * @param list<int> $parts
     * @return list<array{skill: string, part: int, question_ids: array<int, string>}>
     */
    private function buildSections(array $bySkillLevel, Skill $skill, Level $level, array $parts): array
    {
        $partMap = $bySkillLevel[$skill->value]->get($level->value, collect());
        $sections = [];

        foreach ($parts as $part) {
            $questionId = collect($partMap->get($part, []))->first();
            if (! $questionId) {
                return [];
            }

            $sections[] = [
                'skill' => $skill->value,
                'part' => $part,
                'question_ids' => [$questionId],
            ];
        }

        return $sections;
    }

    /**
     * @param array<string, Collection<string, Collection<int, array<int, string>>>> $bySkillLevel
     * @param list<int> $parts
     * @return list<array{skill: string, part: int, question_ids: array<int, string>}>
     */
    private function buildPlacementSections(array $bySkillLevel, Skill $skill, array $parts): array
    {
        $levels = match ($skill) {
            Skill::Listening => [Level::A2, Level::B1, Level::B2],
            Skill::Reading => [Level::B1, Level::B1, Level::B2, Level::B2],
            default => [],
        };

        $sections = [];
        foreach ($parts as $index => $part) {
            $level = $levels[$index] ?? null;
            if (! $level) {
                return [];
            }

            $partMap = $bySkillLevel[$skill->value]->get($level->value, collect());
            $questionId = collect($partMap->get($part, []))->first();
            if (! $questionId) {
                return [];
            }

            $sections[] = [
                'skill' => $skill->value,
                'part' => $part,
                'question_ids' => [$questionId],
            ];
        }

        return $sections;
    }
}
