<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\VstepBand;

final class VstepScoring
{
    /**
     * Convert raw ratio (0.0–1.0) to VSTEP 10-point scale with official rounding.
     */
    public static function score(float $ratio): float
    {
        return self::round($ratio * 10);
    }

    /**
     * VSTEP official rounding: <0.25 → floor, ≥0.25 → 0.5, ≥0.75 → ceil.
     */
    public static function round(float $value): float
    {
        $integer = (int) $value;
        $decimal = $value - $integer;

        return match (true) {
            $decimal >= 0.75 => $integer + 1.0,
            $decimal >= 0.25 => $integer + 0.5,
            default => (float) $integer,
        };
    }

    public static function band(float $overall): ?VstepBand
    {
        return VstepBand::fromScore($overall);
    }
}
