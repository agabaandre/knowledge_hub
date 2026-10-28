<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteLanguage extends Model
{
    public const CACHE_KEY_SELECTOR = 'site_languages.selector_map';

    public const CACHE_KEY_ACTIVE_CODES = 'site_languages.active_locale_codes';

    public const CACHE_KEY_ALL_CODES = 'site_languages.all_locale_codes';

    protected $fillable = [
        'locale_code',
        'name',
        'google_translate_code',
        'flag_emoji',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(static function () {
            static::flushCache();
        });
        static::deleted(static function () {
            static::flushCache();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY_SELECTOR);
        Cache::forget(self::CACHE_KEY_ACTIVE_CODES);
        Cache::forget(self::CACHE_KEY_ALL_CODES);
    }

    /**
     * Config fallback when DB has no rows (e.g. before migration).
     *
     * @return array<string, array{name: string, flag: string, code: string, google_code: string}>
     */
    protected static function fallbackSelectorMap(): array
    {
        $cfg = config('supported_locales.languages', []);
        $out = [];
        foreach ($cfg as $code => $row) {
            $out[$code] = [
                'name' => $row['name'] ?? $code,
                'flag' => $row['flag'] ?? '',
                'code' => $row['code'] ?? $code,
                'google_code' => $row['code'] ?? $code,
            ];
        }

        return $out;
    }

    /**
     * Active languages for header selector & profile (keyed by locale_code).
     *
     * @return array<string, array{name: string, flag: string, code: string, google_code: string}>
     */
    public static function selectorMap(): array
    {
        return Cache::remember(self::CACHE_KEY_SELECTOR, 3600, function () {
            if (! Schema::hasTable('site_languages')) {
                return self::fallbackSelectorMap();
            }

            $rows = static::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['locale_code', 'name', 'google_translate_code', 'flag_emoji']);

            if ($rows->isEmpty()) {
                return self::fallbackSelectorMap();
            }

            $out = [];
            foreach ($rows as $r) {
                $code = $r->locale_code;
                $out[$code] = [
                    'name' => $r->name,
                    'flag' => $r->flag_emoji ?? '',
                    'code' => $code,
                    'google_code' => $r->google_translate_code ?: $code,
                ];
            }

            return $out;
        });
    }

    /**
     * @return list<string>
     */
    public static function activeLocaleCodes(): array
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE_CODES, 3600, function () {
            if (! Schema::hasTable('site_languages')) {
                return array_keys(self::fallbackSelectorMap());
            }

            $codes = static::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('locale_code')
                ->all();

            return $codes !== [] ? $codes : array_keys(self::fallbackSelectorMap());
        });
    }

    /**
     * All locale codes in DB (for translation editor), including inactive.
     *
     * @return list<string>
     */
    public static function allLocaleCodes(): array
    {
        return Cache::remember(self::CACHE_KEY_ALL_CODES, 3600, function () {
            if (! Schema::hasTable('site_languages')) {
                return array_keys(self::fallbackSelectorMap());
            }

            $codes = static::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('locale_code')
                ->all();

            return $codes !== [] ? $codes : array_keys(self::fallbackSelectorMap());
        });
    }

    /**
     * Normalize admin / legacy language values to a locale code (e.g. french → fr).
     */
    public static function normalizeLocaleCode(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $raw = strtolower(trim($raw));
        if ($raw === '') {
            return null;
        }

        $aliases = [
            'english' => 'en',
            'french' => 'fr',
            'francais' => 'fr',
            'français' => 'fr',
            'arabic' => 'ar',
            'spanish' => 'es',
            'portuguese' => 'pt',
            'swahili' => 'sw',
            'kiswahili' => 'sw',
            'amharic' => 'am',
        ];

        if (isset($aliases[$raw])) {
            return $aliases[$raw];
        }

        if (preg_match('/^[a-z]{2}([_-][a-z0-9]+)*$/', $raw)) {
            return str_replace('_', '-', $raw);
        }

        return null;
    }

    /**
     * Site-wide default locale from Admin → Configure (setting.language),
     * constrained to active Site Languages.
     */
    public static function systemDefaultLocale(): string
    {
        $codes = self::activeLocaleCodes();
        $fallback = in_array('en', $codes, true) ? 'en' : ($codes[0] ?? config('supported_locales.default', 'en'));

        $raw = null;
        try {
            if (function_exists('settings')) {
                $raw = settings()->language ?? null;
            }
        } catch (\Throwable $e) {
            $raw = null;
        }

        $normalized = self::normalizeLocaleCode(is_string($raw) ? $raw : null);
        if ($normalized !== null && in_array($normalized, $codes, true)) {
            return $normalized;
        }

        $cfgDefault = self::normalizeLocaleCode((string) config('supported_locales.default', 'en'));
        if ($cfgDefault !== null && in_array($cfgDefault, $codes, true)) {
            return $cfgDefault;
        }

        return $fallback;
    }

    /**
     * Resolve which locale is active for the UI (matches selector keys).
     */
    public static function resolveActiveLocale(?string $userLocale, ?string $googtransRaw, ?string $khubLocale): string
    {
        $map = self::selectorMap();
        $codes = array_keys($map);
        $default = self::systemDefaultLocale();
        if (! in_array($default, $codes, true)) {
            $default = in_array('en', $codes, true) ? 'en' : ($codes[0] ?? 'en');
        }

        if (is_string($khubLocale) && $khubLocale !== '' && in_array($khubLocale, $codes, true)) {
            return $khubLocale;
        }

        if (is_string($userLocale) && $userLocale !== '' && in_array($userLocale, $codes, true)) {
            return $userLocale;
        }

        if (is_string($googtransRaw) && $googtransRaw !== '') {
            $parts = explode('/', $googtransRaw);
            $g = $parts[2] ?? '';
            if ($g !== '') {
                foreach ($map as $loc => $meta) {
                    $gc = $meta['google_code'] ?? $loc;
                    if (strcasecmp($gc, $g) === 0 || strcasecmp((string) $loc, $g) === 0) {
                        return $loc;
                    }
                }
            }
        }

        return $default;
    }

    public function getGoogleCodeAttribute(): string
    {
        return $this->google_translate_code ?: $this->locale_code;
    }
}
