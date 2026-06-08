<?php

namespace App\Support;

use App\Models\SiteLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleDirection
{
    /**
     * @return list<string>
     */
    public static function rtlLocales(): array
    {
        $configured = config('supported_locales.rtl_locales', ['ar']);

        return array_values(array_unique(array_filter(
            is_array($configured) ? $configured : ['ar'],
            static fn ($code) => is_string($code) && $code !== ''
        )));
    }

    public static function isRtl(?string $locale): bool
    {
        if ($locale === null || $locale === '') {
            return false;
        }

        return in_array(strtolower($locale), array_map('strtolower', self::rtlLocales()), true);
    }

    public static function direction(?string $locale): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }

    public static function activeLocale(?Request $request = null): string
    {
        $request = $request ?? request();

        return SiteLanguage::resolveActiveLocale(
            auth()->check() ? (auth()->user()->langauge ?? null) : null,
            $_COOKIE['googtrans'] ?? null,
            $request->cookie((string) config('supported_locales.locale_cookie', 'khub_locale'))
        );
    }

    public static function applyAppLocale(?Request $request = null): string
    {
        $locale = self::activeLocale($request);
        App::setLocale($locale);

        return $locale;
    }
}
