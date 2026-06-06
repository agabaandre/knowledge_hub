<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

final class MetricsCache
{
    private static ?bool $redisAvailable = null;

    public static function store(): Repository
    {
        if (self::redisAvailable()) {
            return Cache::store('redis');
        }

        return Cache::store(config('cache.default', 'file'));
    }

    public static function redisAvailable(): bool
    {
        if (self::$redisAvailable !== null) {
            return self::$redisAvailable;
        }

        try {
            if (! config('cache.stores.redis')) {
                return self::$redisAvailable = false;
            }
            Redis::connection()->ping();

            return self::$redisAvailable = true;
        } catch (\Throwable $e) {
            return self::$redisAvailable = false;
        }
    }

    public static function ttl(string $bucket): int
    {
        return match ($bucket) {
            'live' => 45,
            'filtered' => 60,
            'rcc' => self::redisAvailable() ? 900 : 180,
            'rcc_meta' => self::redisAvailable() ? 3600 : 1800,
            'map_context' => self::redisAvailable() ? 3600 : 1800,
            'default' => self::redisAvailable() ? 21600 : 3600,
        };
    }
}
