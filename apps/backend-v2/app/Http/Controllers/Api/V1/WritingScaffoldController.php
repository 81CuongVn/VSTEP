<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Writing\GenerateWritingScaffoldRequest;
use App\Http\Resources\WritingScaffoldResource;
use App\Services\WritingScaffoldService;

class WritingScaffoldController extends Controller
{
    public function __construct(
        private readonly WritingScaffoldService $service,
    ) {}

    public function generate(GenerateWritingScaffoldRequest $request)
    {
        return new WritingScaffoldResource($this->service->generate(
            $request->validated('question_id'),
            $request->validated('tier'),
        ));
    }
}
