<?php

declare(strict_types=1);

namespace App\Services\WritingScaffoldGenerators;

use App\Models\Question;

interface WritingScaffoldGenerator
{
    public function generate(Question $question, int $tier): array;
}
