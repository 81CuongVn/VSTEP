<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PronunciationService
{
    private string $key;

    private string $region;

    /**
     * Azure short-audio REST API only supports WAV (PCM 16kHz) and OGG (Opus).
     * WebM is NOT supported — must be converted before sending.
     */
    private const AZURE_CONTENT_TYPES = [
        'wav' => 'audio/wav; codecs=audio/pcm; samplerate=16000',
        'ogg' => 'audio/ogg; codecs=opus',
    ];

    public function __construct()
    {
        $this->key = (string) config('services.azure_speech.key', '');
        $this->region = (string) config('services.azure_speech.region', 'southeastasia');
    }

    /**
     * Assess pronunciation from an audio file stored on S3/R2.
     *
     * @return array{transcript: string, accuracy_score: float, fluency_score: float, prosody_score: float, word_errors: array}
     */
    public function assessPronunciation(string $audioPath): array
    {
        $audioContent = Storage::disk('s3')->get($audioPath);

        if (! $audioContent) {
            throw new RuntimeException("Audio file not found: {$audioPath}");
        }

        $this->validateAudioMime($audioContent);
        $this->validateSize($audioContent);

        $detectedFormat = $this->detectFormat($audioContent);

        // Azure REST API doesn't support WebM — convert to WAV PCM 16kHz
        if ($detectedFormat === 'webm') {
            $audioContent = $this->convertToWav($audioContent);
            $detectedFormat = 'wav';
        }

        $contentType = self::AZURE_CONTENT_TYPES[$detectedFormat]
            ?? throw new RuntimeException("Unsupported audio format for Azure: {$detectedFormat}");

        $endpoint = "https://{$this->region}.stt.speech.microsoft.com"
            .'/speech/recognition/conversation/cognitiveservices/v1'
            .'?language=en-US&format=detailed';

        $pronunciationConfig = base64_encode(json_encode([
            'ReferenceText' => '',
            'GradingSystem' => 'HundredMark',
            'Granularity' => 'Word',
            'Dimension' => 'Comprehensive',
            'EnableProsodyAssessment' => 'True',
        ], JSON_THROW_ON_ERROR));

        $response = Http::timeout(30)
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->key,
                'Content-Type' => $contentType,
                'Pronunciation-Assessment' => $pronunciationConfig,
                'Accept' => 'application/json',
            ])
            ->withBody($audioContent, $contentType)
            ->post($endpoint);

        if ($response->failed()) {
            Log::error('azure_speech_http_failed', [
                'status' => $response->status(),
                'audio_path' => $audioPath,
                'body' => $response->body(),
            ]);
            throw new RuntimeException("Azure Speech API returned HTTP {$response->status()}");
        }

        $json = $response->json();

        Log::info('azure_speech_raw_response', [
            'audio_path' => $audioPath,
            'recognition_status' => $json['RecognitionStatus'] ?? 'missing',
            'display_text' => $json['DisplayText'] ?? '',
            'n_best_count' => count($json['NBest'] ?? []),
        ]);

        return $this->parseResponse($json);
    }

    private function parseResponse(array $data): array
    {
        $status = $data['RecognitionStatus'] ?? 'Unknown';

        if ($status !== 'Success') {
            Log::warning('azure_speech_no_recognition', [
                'recognition_status' => $status,
                'display_text' => $data['DisplayText'] ?? '',
            ]);
            throw new RuntimeException("Azure Speech recognition failed. Status: {$status}");
        }

        $nBest = $data['NBest'][0] ?? [];

        $transcript = $nBest['Display'] ?? $data['DisplayText'] ?? '';

        if ($transcript === '') {
            throw new RuntimeException('Azure Speech returned empty transcript despite successful recognition.');
        }

        $wordErrors = [];
        foreach ($nBest['Words'] ?? [] as $word) {
            $errorType = $word['ErrorType'] ?? $word['PronunciationAssessment']['ErrorType'] ?? 'None';
            if ($errorType !== 'None') {
                $wordErrors[] = [
                    'word' => $word['Word'] ?? '',
                    'error_type' => $errorType,
                    'accuracy_score' => $word['AccuracyScore'] ?? $word['PronunciationAssessment']['AccuracyScore'] ?? 0,
                ];
            }
        }

        return [
            'transcript' => $transcript,
            'accuracy_score' => (float) ($nBest['AccuracyScore'] ?? $nBest['PronunciationAssessment']['AccuracyScore'] ?? 0),
            'fluency_score' => (float) ($nBest['FluencyScore'] ?? $nBest['PronunciationAssessment']['FluencyScore'] ?? 0),
            'prosody_score' => (float) ($nBest['ProsodyScore'] ?? $nBest['PronunciationAssessment']['ProsodyScore'] ?? 0),
            'word_errors' => $wordErrors,
        ];
    }

    /**
     * Convert audio to WAV PCM 16kHz mono using ffmpeg.
     * Used for WebM files since Azure REST API doesn't support WebM.
     */
    private function convertToWav(string $content): string
    {
        $inputPath = tempnam(sys_get_temp_dir(), 'audio_in_').'.webm';
        $outputPath = tempnam(sys_get_temp_dir(), 'audio_out_').'.wav';

        try {
            file_put_contents($inputPath, $content);

            $command = sprintf(
                'ffmpeg -y -i %s -ar 16000 -ac 1 -f wav %s 2>&1',
                escapeshellarg($inputPath),
                escapeshellarg($outputPath),
            );

            exec($command, $output, $exitCode);

            if ($exitCode !== 0) {
                Log::error('ffmpeg_conversion_failed', [
                    'exit_code' => $exitCode,
                    'output' => implode("\n", array_slice($output, -5)),
                ]);
                throw new RuntimeException('Failed to convert audio to WAV: ffmpeg exit code '.$exitCode);
            }

            $wavContent = file_get_contents($outputPath);

            if ($wavContent === false || $wavContent === '') {
                throw new RuntimeException('ffmpeg produced empty WAV output.');
            }

            return $wavContent;
        } finally {
            @unlink($inputPath);
            @unlink($outputPath);
        }
    }

    /**
     * Detect actual audio format from file content, ignoring file extension.
     * Browser MediaRecorder often produces WebM even when frontend claims WAV.
     */
    private function detectFormat(string $content): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($content);

        return match ($mime) {
            'audio/x-wav', 'audio/wav' => 'wav',
            'audio/ogg', 'application/ogg' => 'ogg',
            'audio/webm', 'video/webm' => 'webm',
            default => throw new RuntimeException("Cannot detect audio format. MIME: {$mime}"),
        };
    }

    private function validateAudioMime(string $content): void
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($content);
        $allowed = ['audio/x-wav', 'audio/wav', 'audio/ogg', 'application/ogg', 'audio/webm', 'video/webm'];

        if (! in_array($mime, $allowed)) {
            throw new RuntimeException("Invalid audio MIME type: {$mime}. Only WAV, OGG, and WebM are supported.");
        }
    }

    private function validateSize(string $content): void
    {
        $maxBytes = 5 * 1024 * 1024; // 5MB
        if (strlen($content) > $maxBytes) {
            throw new RuntimeException('Audio file exceeds 5MB limit.');
        }
    }

    public static function supportedExtensions(): array
    {
        return ['wav', 'ogg', 'webm'];
    }
}
