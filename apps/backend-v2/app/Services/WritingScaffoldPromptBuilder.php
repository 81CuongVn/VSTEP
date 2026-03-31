<?php

declare(strict_types=1);

namespace App\Services;

class WritingScaffoldPromptBuilder
{
    public function buildSystemPrompt(string $taskType, string $level, int $minWords): string
    {
        return view('generation.writing-scaffold-system', [
            'taskType' => $taskType,
            'level' => $level,
            'minWords' => $minWords,
        ])->render();
    }

    public function buildUserPrompt(string $prompt, array $requiredPoints): string
    {
        return view('generation.writing-scaffold-user', [
            'prompt' => $prompt,
            'requiredPoints' => $requiredPoints,
        ])->render();
    }
}
