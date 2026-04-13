<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $sections = collect($this->blueprint ?? [])
            ->filter(fn (mixed $section) => is_array($section) && array_key_exists('question_ids', $section))
            ->values();

        $allQuestionIds = $sections
            ->flatMap(fn (array $section) => $section['question_ids'] ?? [])
            ->all();

        $itemCounts = ! empty($allQuestionIds)
            ? Question::whereIn('id', $allQuestionIds)
                ->get(['id', 'content'])
                ->mapWithKeys(fn (Question $q) => [
                    $q->id => $this->objectiveItemCount($q),
                ])
            : collect();

        $normalizedSections = $sections
            ->map(function (array $section, int $index) use ($itemCounts): array {
                $questionIds = $section['question_ids'] ?? [];

                $itemCount = collect($questionIds)
                    ->sum(fn (string $id) => $itemCounts->get($id, 0));

                return [
                    'skill' => $section['skill'] ?? null,
                    'part' => $section['part'] ?? $index + 1,
                    'section_type' => in_array($section['skill'] ?? null, ['listening', 'reading'], true)
                        ? 'objective_group'
                        : (($section['skill'] ?? null) === 'writing' ? 'writing_task' : 'speaking_part'),
                    'title' => $section['title'] ?? null,
                    'instructions' => $section['instructions'] ?? null,
                    'objective_item_count' => $itemCount,
                    'entry_count' => count($questionIds),
                    'bank_entry_ids' => $questionIds,
                    'question_count' => $itemCount ?: count($questionIds),
                    'question_ids' => $questionIds,
                    'order' => $section['order'] ?? $index + 1,
                ];
            })
            ->all();

        $objectiveQuestionCount = collect($normalizedSections)
            ->whereIn('skill', ['listening', 'reading'])
            ->sum('question_count');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'level' => $this->level,
            'type' => $this->type,
            'duration_minutes' => $this->duration_minutes,
            'objective_question_count' => $objectiveQuestionCount,
            'section_count' => count($normalizedSections),
            'sections' => $normalizedSections,
        ];
    }

    private function objectiveItemCount(Question $question): int
    {
        $content = $question->content;
        if (! is_array($content)) {
            return 0;
        }

        $items = $content['items'] ?? [];

        return is_array($items) ? count($items) : 0;
    }
}
