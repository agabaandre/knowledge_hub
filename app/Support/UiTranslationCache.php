<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Repository;

/**
 * Redis-backed (when available) cache for Language Management / UI locale groups
 * and AI-assisted translation chunks.
 */
final class UiTranslationCache
{
    private const VERSION_KEY = 'ui_i18n:version';

    private const PREFIX = 'ui_i18n:g';

    private const AI_PREFIX = 'ui_i18n:ai';

    public static function store(): Repository
    {
        return MetricsCache::store();
    }

    public static function ttl(): int
    {
        return MetricsCache::ttl('ui_translations');
    }

    public static function aiTtl(): int
    {
        return MetricsCache::ttl('ui_translations_ai');
    }

    public static function version(): int
    {
        try {
            $v = self::store()->get(self::VERSION_KEY, 1);

            return is_numeric($v) ? max(1, (int) $v) : 1;
        } catch (\Throwable $e) {
            return 1;
        }
    }

    public static function bumpVersion(): void
    {
        try {
            $store = self::store();
            if (method_exists($store->getStore(), 'increment')) {
                try {
                    $store->increment(self::VERSION_KEY);

                    return;
                } catch (\Throwable $e) {
                    // fall through
                }
            }
            $store->forever(self::VERSION_KEY, self::version() + 1);
        } catch (\Throwable $e) {
            // Cache backend unavailable — runtime still works without cache.
        }
    }

    public static function groupKey(string $locale, string $group): string
    {
        return self::PREFIX.':v'.self::version().':'.strtolower(trim($locale)).':'.$group;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getGroup(string $locale, string $group): ?array
    {
        try {
            $cached = self::store()->get(self::groupKey($locale, $group));

            return is_array($cached) ? $cached : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $lines
     */
    public static function putGroup(string $locale, string $group, array $lines): void
    {
        try {
            self::store()->put(self::groupKey($locale, $group), $lines, self::ttl());
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public static function forgetGroup(string $locale, string $group): void
    {
        try {
            self::store()->forget(self::groupKey($locale, $group));
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * After Language Management save: drop runtime + editor caches and bump version.
     */
    public static function invalidateAfterSave(string $locale, string $group): void
    {
        self::forgetGroup($locale, $group);
        self::forgetGroup($locale.'#merged', $group);
        self::bumpVersion();
    }

    public static function flushAll(): void
    {
        self::bumpVersion();
    }

    /**
     * Stable key for an AI translate chunk (target label + English key/value map).
     *
     * @param  array<string, string>  $englishKeyed
     */
    public static function aiChunkKey(string $targetLanguageLabel, array $englishKeyed): string
    {
        ksort($englishKeyed);
        $payload = strtolower(trim($targetLanguageLabel)).'|'.json_encode($englishKeyed, JSON_UNESCAPED_UNICODE);

        return self::AI_PREFIX.':'.hash('sha256', $payload);
    }

    /**
     * @param  array<string, string>  $englishKeyed
     * @return array<string, string>|null
     */
    public static function getAiChunk(string $targetLanguageLabel, array $englishKeyed): ?array
    {
        try {
            $cached = self::store()->get(self::aiChunkKey($targetLanguageLabel, $englishKeyed));

            return is_array($cached) ? $cached : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param  array<string, string>  $englishKeyed
     * @param  array<string, string>  $translations
     */
    public static function putAiChunk(string $targetLanguageLabel, array $englishKeyed, array $translations): void
    {
        if ($translations === []) {
            return;
        }

        try {
            self::store()->put(
                self::aiChunkKey($targetLanguageLabel, $englishKeyed),
                $translations,
                self::aiTtl()
            );
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
