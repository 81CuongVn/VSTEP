<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Services\WritingScaffoldGenerators\FreeformScaffoldGenerator;
use App\Services\WritingScaffoldGenerators\GuidedScaffoldGenerator;
use App\Services\WritingScaffoldGenerators\TemplateScaffoldGenerator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WritingScaffoldService
{
    public function __construct(
        private readonly TemplateScaffoldGenerator $templateGenerator,
        private readonly GuidedScaffoldGenerator $guidedGenerator,
        private readonly FreeformScaffoldGenerator $freeformGenerator,
    ) {}

    public function generate(string $questionId, int $tier): array
    {
        $question = Question::find($questionId);

        if (! $question) {
            throw (new ModelNotFoundException)->setModel(Question::class, [$questionId]);
        }

        return $this->forQuestion($question, $tier);
    }

    public function forQuestion(Question $question, int $tier): array
    {
        return match ($tier) {
            1 => $this->templateGenerator->generate($question, $tier),
            2 => $this->guidedGenerator->generate($question, $tier),
            default => $this->freeformGenerator->generate($question, $tier),
        };
    }
}
