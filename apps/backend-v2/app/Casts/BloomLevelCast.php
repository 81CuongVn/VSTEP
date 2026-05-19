<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\BloomLevel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class BloomLevelCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BloomLevel
    {
        if ($value === null) {
            return null;
        }

        return BloomLevel::from($this->normalize((string) $value));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BloomLevel) {
            return $value->value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException('Bloom level must be a string or BloomLevel enum.');
        }

        return $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        return str($value)->trim()->lower()->snake()->toString();
    }
}
