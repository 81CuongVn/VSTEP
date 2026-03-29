<?php

declare(strict_types=1);

namespace App\Http\Requests\Exam;

use App\Enums\ExamType;
use App\Enums\Level;
use App\Enums\Skill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'level' => ['sometimes', 'string', Rule::enum(Level::class)],
            'type' => ['sometimes', 'string', Rule::enum(ExamType::class)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'blueprint' => ['nullable', 'array'],
            'blueprint.*.skill' => ['required_with:blueprint', 'string', Rule::enum(Skill::class)],
            'blueprint.*.part' => ['nullable', 'integer', 'min:1'],
            'blueprint.*.title' => ['nullable', 'string', 'max:255'],
            'blueprint.*.instructions' => ['nullable', 'string'],
            'blueprint.*.order' => ['nullable', 'integer', 'min:1'],
            'blueprint.*.question_ids' => ['required_with:blueprint', 'array', 'min:1'],
            'blueprint.*.question_ids.*' => ['uuid', 'exists:questions,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
