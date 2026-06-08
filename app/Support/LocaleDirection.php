<?php

namespace App\Support;

class LocaleDirection
{
    /**
     * @return list<string>
     */
    public static function rtlLocaleCodes(): array
    {
        $configured = config('supported_locales.rtl_locales', ['ar']);

        return array_values(array_unique(array_filter(array_map(
            static fn ($code) => is_string($code) ? strtolower(trim($code)) : '',
            is_array($configured) ? $configured : ['ar']
        ))));
    }

    public static function isRtl(?string $locale = null): bool
    {
        $locale = strtolower(trim((string) ($locale ?? app()->getLocale())));

        if ($locale === '') {
            return false;
        }

        return in_array($locale, self::rtlLocaleCodes(), true);
    }

    public static function htmlDir(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }

    /**
     * Google Translate language codes that should use RTL layout.
     *
     * @return list<string>
     */
    public static function rtlGoogleCodes(): array
    {
        $map = config('supported_locales.rtl_google_codes');
        if (is_array($map) && $map !== []) {
            return array_values(array_unique(array_filter(array_map(
                static fn ($code) => is_string($code) ? strtolower(trim($code)) : '',
                $map
            ))));
        }

        return ['ar', 'he', 'fa', 'ur'];
    }

    public static function isRtlGoogleCode(?string $googleCode): bool
    {
        $googleCode = strtolower(trim((string) $googleCode));

        return $googleCode !== '' && in_array($googleCode, self::rtlGoogleCodes(), true);
    }
}
