<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SubmitQuestions implements Tool
{
    private ?array $result = null;

    public function description(): Stringable|string
    {
        return <<<'DESC'
        Submit generated questions as a JSON string. The JSON must be an array of objects, each with:
        - "topic": string (short topic label)
        - "bloom_level": string (remember|understand|apply|analyze|evaluate|create)
        - "prompt": string (the full question prompt in English)
        - "knowledge_points": array of strings (exact names from the Knowledge Points Pool)

        Example: [{"topic":"Remote Work","bloom_level":"analyze","prompt":"Write an essay...","knowledge_points":["Complex Sentences","Topic-Specific Vocabulary"]}]
        DESC;
    }

    public function handle(Request $request): Stringable|string
    {
        $json = (string) $request['questions_json'];
        $parsed = json_decode($json, true);

        if (! is_array($parsed) || empty($parsed)) {
            return 'Error: Invalid JSON. Please provide a valid JSON array of question objects.';
        }

        $this->result = $parsed;

        return 'Questions submitted successfully. Count: '.count($parsed);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'questions_json' => $schema->string()
                ->description('JSON array of question objects. Each object: {topic, bloom_level, prompt, knowledge_points}')
                ->required(),
        ];
    }

    public function getResult(): ?array
    {
        return $this->result;
    }
}
