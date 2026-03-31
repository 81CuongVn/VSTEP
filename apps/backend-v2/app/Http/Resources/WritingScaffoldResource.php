<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingScaffoldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'question_id' => $this['question_id'],
            'tier' => $this['tier'],
            'type' => $this['type'],
            'payload' => $this['payload'],
        ];
    }
}
