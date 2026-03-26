<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\SubmitQuestions;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('local')]
#[Model('gpt-5.4')]
#[MaxSteps(3)]
#[MaxTokens(4096)]
#[Timeout(120)]
class QuestionGenerator implements Agent, HasTools
{
    use Promptable;

    private SubmitQuestions $submitTool;

    public function __construct(
        private readonly string $constraints,
    ) {
        $this->submitTool = new SubmitQuestions;
    }

    public function instructions(): Stringable|string
    {
        return $this->constraints;
    }

    public function tools(): iterable
    {
        return [$this->submitTool];
    }

    public function getResult(): ?array
    {
        return $this->submitTool->getResult();
    }
}
