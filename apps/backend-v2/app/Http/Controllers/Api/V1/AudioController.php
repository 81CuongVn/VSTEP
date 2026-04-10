<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AudioStorageService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class AudioController extends Controller
{
    private const ALLOWED_PREFIXES = ['listening/', 'reference_audio/', 'audio/', 'speaking/'];

    private const PRESIGN_SECONDS = 3600; // 1 hour

    public function __construct(
        private readonly AudioStorageService $storage,
    ) {}

    public function presignRead(Request $request)
    {
        $path = $request->validate([
            'path' => ['required', 'string', 'max:255'],
        ])['path'];

        if (! $this->isAllowedPath($path)) {
            throw ValidationException::withMessages([
                'path' => ['Access denied.'],
            ]);
        }

        try {
            if (! $this->storage->exists($path)) {
                $fallbackUrl = $this->localFallbackUrl($path);
                if ($fallbackUrl !== null) {
                    return $this->presignedResponse($fallbackUrl);
                }

                throw ValidationException::withMessages([
                    'path' => ['File not found.'],
                ]);
            }

            $url = $this->storage->temporaryUrl($path, self::PRESIGN_SECONDS);
        } catch (ServiceUnavailableHttpException $e) {
            $fallbackUrl = $this->localFallbackUrl($path);
            if ($fallbackUrl !== null) {
                return $this->presignedResponse($fallbackUrl);
            }

            throw $e;
        }

        return $this->presignedResponse($url);
    }

    private function presignedResponse(string $url)
    {
        return response()->json(['data' => [
            'url' => $url,
            'expires_in' => self::PRESIGN_SECONDS,
        ]]);
    }

    private function localFallbackUrl(string $path): ?string
    {
        if (! app()->isLocal()) {
            return null;
        }

        if (str_starts_with($path, 'speaking/')) {
            return null;
        }

        $fallbackPath = public_path('e2e-speaking-sample.wav');

        if (! is_file($fallbackPath)) {
            return null;
        }

        return url('/e2e-speaking-sample.wav');
    }

    private function isAllowedPath(string $path): bool
    {
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
