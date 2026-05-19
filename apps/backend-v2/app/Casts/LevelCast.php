<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\Level;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class LevelCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Level
    {
        if ($value === null) {
            return null;
        }

        return Level::from($this->normalize((string) $value));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Level) {
            return $value->value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException('Level must be a string or Level enum.');
        }

        return $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        return str($value)->trim()->upper()->toString();
    }
}
