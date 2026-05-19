<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\Skill;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class SkillCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Skill
    {
        if ($value === null) {
            return null;
        }

        return Skill::from($this->normalize((string) $value));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Skill) {
            return $value->value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException('Skill must be a string or Skill enum.');
        }

        return $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        return str($value)->trim()->lower()->toString();
    }
}
