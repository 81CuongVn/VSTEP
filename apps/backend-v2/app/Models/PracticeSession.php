<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Level;
use App\Enums\PracticeMode;
use App\Enums\Skill;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'skill', 'mode', 'level', 'config', 'progress', 'summary', 'started_at', 'completed_at'])]
#[Hidden(['user_id'])]
class PracticeSession extends BaseModel
{
    protected function casts(): array
    {
        return [
            'skill' => Skill::class,
            'mode' => PracticeMode::class,
            'level' => Level::class,
            'config' => 'array',
            'progress' => 'array',
            'summary' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function forUser(Builder $query, string $userId): void
    {
        $query->where('user_id', $userId);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNull('completed_at');
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->whereNotNull('completed_at');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function currentIndex(): int
    {
        return $this->progress['current_index'] ?? 0;
    }

    public function items(): array
    {
        return $this->progress['items'] ?? [];
    }

    public function totalItems(): int
    {
        return $this->config['items_count'] ?? 5;
    }

    public function hasMoreItems(): bool
    {
        return $this->currentIndex() < $this->totalItems();
    }
}
