<?php

declare(strict_types=1);

namespace App\Providers;

use App\Ai\LocalOpenAiGateway;
use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Laravel\Ai\AiManager;
use Laravel\Ai\Providers\OpenAiProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('db.connector.pgsql', function (): NeonPostgresConnector {
            return new NeonPostgresConnector;
        });

        $this->app->resolving(AiManager::class, function (AiManager $ai, $app): void {
            $ai->extend('local', function ($app, array $config) {
                return new OpenAiProvider(
                    new LocalOpenAiGateway($app['events']),
                    $config,
                    $app->make(Dispatcher::class),
                );
            });
        });
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        // Seed database on boot if configured and database is empty
        if (config('app.seed_on_boot') && $this->shouldSeedDatabase()) {
            $this->seedDatabase();
        }
    }

    private function shouldSeedDatabase(): bool
    {
        try {
            // Check if users table has data (simple check)
            return \App\Models\User::count() === 0;
        } catch (\Exception) {
            return false;
        }
    }

    private function seedDatabase(): void
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\Seeders\DatabaseSeeder']);
        } catch (\Exception $e) {
            \Log::warning('Database seeding failed: ' . $e->getMessage());
        }
    }
}
