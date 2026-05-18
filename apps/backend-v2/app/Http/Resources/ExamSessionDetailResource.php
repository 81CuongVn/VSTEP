<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSessionDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $exam = $this->whenLoaded('exam');
        $questions = $this->whenLoaded('questions');
        $answers = $this->whenLoaded('answers');
        $submissions = $this->whenLoaded('submissions');

        return [
            'session' => [
                ...parent::toArray($request),
                'exam' => null,
                'questions' => null,
                'answers' => null,
                'submissions' => null,
            ],
            'exam' => $exam ? new ExamSummaryResource($exam) : null,
            'questions' => $questions ? SessionQuestionResource::collection($questions) : [],
            'answers' => $answers ? ExamAnswerResource::collection($answers) : [],
            'submissions' => $submissions ? SubmissionResource::collection($submissions) : [],
            'progress' => [
                'answered' => $answers ? $answers->count() : 0,
                'total' => $questions ? $questions->count() : 0,
            ],
        ];
    }
}
