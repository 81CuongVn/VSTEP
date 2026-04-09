<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\SpeakingUploadService;
use Illuminate\Foundation\Http\FormRequest;

class UploadAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => [
                'required',
                'file',
                'max:'.(int) ceil(SpeakingUploadService::maxFileSize() / 1024),
                'mimetypes:'.implode(',', SpeakingUploadService::allowedMimeValidationTypes()),
            ],
        ];
    }
}
