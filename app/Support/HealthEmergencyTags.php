<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class HealthEmergencyTags
{
    public static function from($tags): Collection
    {
        return collect($tags ?? [])
            ->filter(fn ($tag) => self::isDeclared($tag))
            ->values();
    }

    public static function isDeclared($tag): bool
    {
        if (! is_object($tag)) {
            return false;
        }

        $value = $tag->is_health_emergency ?? false;

        return $value === true || $value === 1 || $value === '1';
    }
}
