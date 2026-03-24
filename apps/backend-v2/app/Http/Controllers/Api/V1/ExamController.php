<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ExamSessionResource;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamService $service,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ExamResource::collection($this->service->listExams($request->query()));
    }

    public function show(Exam $exam): ExamResource
    {
        return new ExamResource($exam);
    }

    public function store(StoreExamRequest $request): JsonResponse
    {
        $exam = $this->service->createExam($request->validated(), $request->user()->id);

        return (new ExamResource($exam))->response()->setStatusCode(201);
    }

    public function update(UpdateExamRequest $request, Exam $exam): ExamResource
    {
        $exam->update($request->validated());

        return new ExamResource($exam);
    }

    public function start(Request $request, Exam $exam): JsonResponse
    {
        $session = $this->service->startSession($exam, $request->user()->id);

        return (new ExamSessionResource($session))->response()->setStatusCode(201);
    }
}
