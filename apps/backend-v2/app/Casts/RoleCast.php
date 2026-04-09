<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\Role;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class RoleCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Role
    {
        if ($value === null) {
            return null;
        }

        return Role::from($this->normalize($value));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Role) {
            return $value->value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException('Role must be a string or Role enum.');
        }

        return $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        return match ($value) {
            'teacher' => Role::Instructor->value,
            default => $value,
        };
    }
}
