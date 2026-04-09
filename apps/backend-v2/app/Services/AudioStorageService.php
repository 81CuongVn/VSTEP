<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class AudioStorageService
{
    public function exists(string $path): bool
    {
        try {
            return Storage::disk('s3')->exists($path);
        } catch (\Throwable $e) {
            throw new ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.', $e);
        }
    }

    /**
     * @return array{url: string, headers?: array<string, string>}
     */
    public function temporaryUploadUrl(string $path, string $contentType, int $expiresInSeconds): array
    {
        try {
            $upload = Storage::disk('s3')->temporaryUploadUrl(
                $path,
                now()->addSeconds($expiresInSeconds),
                ['ContentType' => $contentType],
            );

            return [
                'url' => $upload['url'],
                'headers' => $this->normalizeHeaders($upload['headers'] ?? []),
            ];
        } catch (\Throwable $e) {
            throw new ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.', $e);
        }
    }

    /**
     * @param  array<string, mixed>  $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $value = $value[0] ?? '';
            }

            if (! is_string($value) || $value === '') {
                continue;
            }

            $normalized[$name] = $value;
        }

        return $normalized;
    }

    public function temporaryUrl(string $path, int $expiresInSeconds): string
    {
        try {
            return Storage::disk('s3')->temporaryUrl($path, now()->addSeconds($expiresInSeconds));
        } catch (\Throwable $e) {
            throw new ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.', $e);
        }
    }

    public function storeUploadedFile(string $path, UploadedFile $file, string $contentType): void
    {
        $stream = fopen($file->getRealPath(), 'rb');

        if ($stream === false) {
            throw new ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.');
        }

        try {
            Storage::disk('s3')->put($path, $stream, [
                'ContentType' => $contentType,
            ]);
        } catch (\Throwable $e) {
            throw new ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.', $e);
        } finally {
            fclose($stream);
        }
    }
}
