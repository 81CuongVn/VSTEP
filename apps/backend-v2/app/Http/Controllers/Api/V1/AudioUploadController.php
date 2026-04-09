<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadAudioRequest;
use App\Services\SpeakingUploadService;

class AudioUploadController extends Controller
{
    public function __construct(
        private readonly SpeakingUploadService $service,
    ) {}

    public function store(UploadAudioRequest $request)
    {
        $data = $this->service->storeAudioUpload(
            $request->user()->id,
            $request->file('audio'),
        );

        return response()->json(['data' => $data], 201);
    }
}
