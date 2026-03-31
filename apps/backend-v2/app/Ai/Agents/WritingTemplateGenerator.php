<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('local')]
#[Model('gpt-5.4')]
class WritingTemplateGenerator implements Agent, HasProviderOptions, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private readonly string $instructions,
    ) {}

    public function instructions(): Stringable|string
    {
        return $this->instructions;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'sections' => $schema->array()->items(
                $schema->object([
                    'title' => $schema->string()->required(),
                    'parts' => $schema->array()->items(
                        $schema->object([
                            'type' => $schema->string()->enum(['text', 'blank'])->required(),
                            'content' => $schema->string()->nullable()->required(),
                            'id' => $schema->string()->nullable()->required(),
                            'label' => $schema->string()->nullable()->required(),
                            'variant' => $schema->string()->enum(['content', 'transition'])->nullable()->required(),
                            'hints' => $schema->object([
                                'b1' => $schema->array()->items($schema->string())->nullable()->required(),
                                'b2' => $schema->array()->items($schema->string())->nullable()->required(),
                            ])->withoutAdditionalProperties()->nullable()->required(),
                        ])->withoutAdditionalProperties()
                    )->required(),
                ])->withoutAdditionalProperties()
            )->required(),
        ];
    }

    public function providerOptions(Lab|string $provider): array
    {
        if ($provider !== 'local') {
            return [];
        }

        return [
            'reasoning' => [
                'effort' => 'none',
            ],
        ];
    }
}
