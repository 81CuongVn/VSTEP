<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\AudioStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AudioUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_uploads_audio_via_backend_and_returns_storage_path(): void
    {
        Storage::fake('s3');

        $user = User::create([
            'full_name' => 'Learner',
            'email' => 'audio-store@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'recording.wav',
            $this->fakeWavPayload(),
            'audio/wav',
        );

        $response = $this->actingAs($user, 'api')
            ->post('/api/v1/uploads/audio', [
                'audio' => $file,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.audio_path', fn (string $path) => str_starts_with($path, "speaking/{$user->id}/") && str_ends_with($path, '.wav'));

        Storage::disk('s3')->assertExists($response->json('data.audio_path'));
    }

    #[Test]
    public function it_accepts_browser_wav_variants_when_uploading_via_backend(): void
    {
        Storage::fake('s3');

        $user = User::create([
            'full_name' => 'Learner',
            'email' => 'audio-store-xwav@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $response = $this->actingAs($user, 'api')
            ->post('/api/v1/uploads/audio', [
                'audio' => UploadedFile::fake()->createWithContent(
                    'recording.wav',
                    $this->fakeWavPayload(),
                    'audio/x-wav',
                ),
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.audio_path', fn (string $path) => str_ends_with($path, '.wav'));
    }

    #[Test]
    public function it_returns_503_when_backend_audio_upload_storage_is_unavailable(): void
    {
        $user = User::create([
            'full_name' => 'Learner',
            'email' => 'audio-store-fail@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $this->actingAs($user, 'api');
        $this->app->bind(AudioStorageService::class, fn () => new class extends AudioStorageService
        {
            public function storeUploadedFile(string $path, UploadedFile $file, string $contentType): void
            {
                throw new \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException(null, 'Audio storage is temporarily unavailable.');
            }
        });

        $this->post('/api/v1/uploads/audio', [
            'audio' => UploadedFile::fake()->createWithContent(
                'recording.wav',
                $this->fakeWavPayload(),
                'audio/wav',
            ),
        ])
            ->assertStatus(503)
            ->assertJson([
                'message' => 'Audio storage is temporarily unavailable.',
            ]);
    }

    private function fakeWavPayload(): string
    {
        return 'RIFF....WAVEfmt ';
    }
}
