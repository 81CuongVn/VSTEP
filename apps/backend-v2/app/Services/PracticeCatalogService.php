<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Level;
use App\Enums\PracticeMode;
use App\Enums\Skill;
use App\Models\Question;
use Illuminate\Support\Collection;

class PracticeCatalogService
{
    /**
     * @return array{
     *     levels: list<string>,
     *     skills: list<array{
     *         key: string,
     *         label: string,
     *         description: string,
     *         section_type: string,
     *         supported_modes: list<string>,
     *         question_count: int,
     *         topic_count: int,
     *         parts: list<array{
     *             part: int,
     *             code: string,
     *             title: string,
     *             short_label: string,
     *             description: string,
     *             section_type: string,
     *             recommended_items: int,
     *             question_count: int,
     *             topic_count: int,
     *             bands: list<array{
     *                 level: string,
     *                 available: bool,
     *                 question_count: int,
     *                 topic_count: int,
     *                 topics: list<string>
     *             }>
     *         }>
     *     }>
     * }
     */
    public function getCatalog(): array
    {
        $rows = Question::query()
            ->select(['skill', 'level', 'part', 'topic'])
            ->where('is_active', true)
            ->get()
            ->map(fn (Question $question): array => [
                'skill' => $question->skill->value,
                'level' => $question->level->value,
                'part' => $question->part,
                'topic' => $question->topic,
            ]);

        $levels = [Level::B1->value, Level::B2->value, Level::C1->value];
        $catalog = [];

        foreach ($this->metadata() as $skillKey => $skillMeta) {
            $skillRows = $rows->where('skill', $skillKey);
            $parts = [];

            foreach ($skillMeta['parts'] as $partMeta) {
                $partRows = $skillRows->where('part', $partMeta['part']);
                $bands = [];

                foreach ($levels as $level) {
                    $bandRows = $partRows->where('level', $level);
                    $topics = $this->topicsFor($bandRows);

                    $bands[] = [
                        'level' => $level,
                        'available' => $bandRows->isNotEmpty(),
                        'question_count' => $bandRows->count(),
                        'topic_count' => count($topics),
                        'topics' => $topics,
                    ];
                }

                $parts[] = [
                    'part' => $partMeta['part'],
                    'code' => $partMeta['code'],
                    'title' => $partMeta['title'],
                    'short_label' => $partMeta['short_label'],
                    'description' => $partMeta['description'],
                    'section_type' => $partMeta['section_type'],
                    'recommended_items' => $partMeta['recommended_items'],
                    'question_count' => $partRows->count(),
                    'topic_count' => count($this->topicsFor($partRows)),
                    'bands' => $bands,
                ];
            }

            $catalog[] = [
                'key' => $skillKey,
                'label' => $skillMeta['label'],
                'description' => $skillMeta['description'],
                'section_type' => $skillMeta['section_type'],
                'supported_modes' => $this->supportedModesFor(Skill::from($skillKey)),
                'question_count' => $skillRows->whereIn('level', $levels)->count(),
                'topic_count' => count($this->topicsFor($skillRows->whereIn('level', $levels))),
                'parts' => $parts,
            ];
        }

        return [
            'levels' => $levels,
            'skills' => $catalog,
        ];
    }

    /**
     * @return array<string, array{
     *     label: string,
     *     description: string,
     *     section_type: string,
     *     parts: list<array{
     *         part: int,
     *         code: string,
     *         title: string,
     *         short_label: string,
     *         description: string,
     *         section_type: string,
     *         recommended_items: int
     *     }>
     * }>
     */
    private function metadata(): array
    {
        return [
            Skill::Listening->value => [
                'label' => 'Listening',
                'description' => 'Luyện nghe thông báo ngắn, hội thoại và bài nói học thuật theo format VSTEP.',
                'section_type' => 'objective_group',
                'parts' => [
                    [
                        'part' => 1,
                        'code' => 'listening_part_1',
                        'title' => 'Part 1',
                        'short_label' => 'Thông báo ngắn',
                        'description' => 'Nghe các thông báo hoặc đoạn độc thoại ngắn và chọn đáp án đúng.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 8,
                    ],
                    [
                        'part' => 2,
                        'code' => 'listening_part_2',
                        'title' => 'Part 2',
                        'short_label' => 'Hội thoại',
                        'description' => 'Nghe hội thoại đời sống hoặc học thuật ngắn và xác định thông tin chính.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 8,
                    ],
                    [
                        'part' => 3,
                        'code' => 'listening_part_3',
                        'title' => 'Part 3',
                        'short_label' => 'Bài giảng',
                        'description' => 'Nghe bài nói dài hơn, theo dõi luận điểm và suy luận ý nghĩa.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 10,
                    ],
                ],
            ],
            Skill::Reading->value => [
                'label' => 'Reading',
                'description' => 'Luyện đọc hiểu từ câu ngắn đến bài đọc dài, bám sát các phần VSTEP.',
                'section_type' => 'objective_group',
                'parts' => [
                    [
                        'part' => 1,
                        'code' => 'reading_part_1',
                        'title' => 'Part 1',
                        'short_label' => 'Đọc cơ bản',
                        'description' => 'Đọc câu hoặc đoạn ngắn để nắm ý chính và thông tin trực tiếp.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 10,
                    ],
                    [
                        'part' => 2,
                        'code' => 'reading_part_2',
                        'title' => 'Part 2',
                        'short_label' => 'Suy luận',
                        'description' => 'Đọc hiểu các đoạn văn yêu cầu suy luận, đoán nghĩa và xác định mục đích.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 10,
                    ],
                    [
                        'part' => 3,
                        'code' => 'reading_part_3',
                        'title' => 'Part 3',
                        'short_label' => 'Điền từ / nối đoạn',
                        'description' => 'Xử lý dạng gap-fill hoặc matching để kiểm tra mạch liên kết văn bản.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 8,
                    ],
                    [
                        'part' => 4,
                        'code' => 'reading_part_4',
                        'title' => 'Part 4',
                        'short_label' => 'Bài đọc dài',
                        'description' => 'Đọc bài dài hơn với câu hỏi chi tiết, thái độ tác giả và suy luận.',
                        'section_type' => 'objective_group',
                        'recommended_items' => 10,
                    ],
                ],
            ],
            Skill::Writing->value => [
                'label' => 'Writing',
                'description' => 'Luyện viết thư và bài luận theo đúng task type của VSTEP B1/B2/C1.',
                'section_type' => 'writing_task',
                'parts' => [
                    [
                        'part' => 1,
                        'code' => 'writing_part_1',
                        'title' => 'Part 1',
                        'short_label' => 'Viết thư',
                        'description' => 'Viết email hoặc thư phản hồi đúng mục tiêu giao tiếp và yêu cầu đề bài.',
                        'section_type' => 'writing_task',
                        'recommended_items' => 1,
                    ],
                    [
                        'part' => 2,
                        'code' => 'writing_part_2',
                        'title' => 'Part 2',
                        'short_label' => 'Viết luận',
                        'description' => 'Viết bài luận trình bày quan điểm, phân tích và bảo vệ lập luận.',
                        'section_type' => 'writing_task',
                        'recommended_items' => 1,
                    ],
                ],
            ],
            Skill::Speaking->value => [
                'label' => 'Speaking',
                'description' => 'Luyện nói theo 3 phần VSTEP: social interaction, solution discussion, topic development.',
                'section_type' => 'speaking_part',
                'parts' => [
                    [
                        'part' => 1,
                        'code' => 'speaking_part_1',
                        'title' => 'Part 1',
                        'short_label' => 'Giao tiếp xã hội',
                        'description' => 'Trả lời câu hỏi quen thuộc, giới thiệu bản thân và trải nghiệm cá nhân.',
                        'section_type' => 'speaking_part',
                        'recommended_items' => 1,
                    ],
                    [
                        'part' => 2,
                        'code' => 'speaking_part_2',
                        'title' => 'Part 2',
                        'short_label' => 'Thảo luận giải pháp',
                        'description' => 'So sánh lựa chọn, đưa giải pháp và giải thích lý do thuyết phục.',
                        'section_type' => 'speaking_part',
                        'recommended_items' => 1,
                    ],
                    [
                        'part' => 3,
                        'code' => 'speaking_part_3',
                        'title' => 'Part 3',
                        'short_label' => 'Phát triển chủ đề',
                        'description' => 'Phát triển quan điểm sâu hơn với ví dụ, lập luận và mở rộng ý tưởng.',
                        'section_type' => 'speaking_part',
                        'recommended_items' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function supportedModesFor(Skill $skill): array
    {
        return collect(PracticeMode::cases())
            ->filter(fn (PracticeMode $mode) => $mode->availableForSkill($skill))
            ->map(fn (PracticeMode $mode) => $mode->value)
            ->values()
            ->all();
    }

    /**
     * @param Collection<int, array{skill: string, level: string, part: int, topic: ?string}> $rows
     * @return list<string>
     */
    private function topicsFor(Collection $rows): array
    {
        return $rows
            ->pluck('topic')
            ->filter(fn (?string $topic) => $topic !== null && $topic !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
