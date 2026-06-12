<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Repository;

final class SearchCache
{
    public const HYBRID_VERSION_KEY = 'search_cache:hybrid_version';

    public const AI_INSIGHTS_VERSION_KEY = 'search_cache:ai_insights_version';

    public static function store(): Repository
    {
        return MetricsCache::store();
    }

    public static function hybridVersion(): int
    {
        return max(1, (int) self::store()->get(self::HYBRID_VERSION_KEY, 1));
    }

    public static function aiInsightsVersion(): int
    {
        return max(1, (int) self::store()->get(self::AI_INSIGHTS_VERSION_KEY, 1));
    }

    /**
     * Invalidate hybrid Meilisearch+SQL ID cache and AI insight payloads.
     */
    public static function bumpAll(): void
    {
        try {
            MetricsCache::store()->forget('meilisearch_health_ok');
        } catch (\Throwable $e) {
            // Non-fatal when cache store is unavailable.
        }

        self::bumpHybrid();
        self::bumpAiInsights();
    }

    public static function bumpHybrid(): void
    {
        self::incrementVersion(self::HYBRID_VERSION_KEY);
    }

    public static function bumpAiInsights(): void
    {
        self::incrementVersion(self::AI_INSIGHTS_VERSION_KEY);
    }

    private static function incrementVersion(string $key): void
    {
        $store = self::store();
        $next = max(1, (int) $store->get($key, 0)) + 1;
        $store->forever($key, $next);
    }
}
