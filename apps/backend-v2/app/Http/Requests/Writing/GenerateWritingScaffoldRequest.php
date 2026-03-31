<?php

declare(strict_types=1);

namespace App\Http\Requests\Writing;

use Illuminate\Foundation\Http\FormRequest;

class GenerateWritingScaffoldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'string', 'exists:questions,id'],
            'tier' => ['required', 'integer', 'min:1', 'max:3'],
        ];
    }
}
