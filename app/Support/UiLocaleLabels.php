<?php

namespace App\Support;

use App\Services\UiTranslationService;
use Illuminate\Support\Facades\File;

class UiLocaleLabels
{
    /**
     * Homepage section title: language-management string, then admin setting override, then English default.
     */
    public static function homeSection(string $key): string
    {
        $translation = __("home_sections.{$key}");
        if ($translation !== "home_sections.{$key}") {
            return $translation;
        }

        $settingsMap = config('supported_locales.home_section_settings', []);
        $settingsKey = $settingsMap[$key] ?? null;
        if (is_string($settingsKey) && $settingsKey !== '') {
            $custom = settings()->{$settingsKey} ?? null;
            if (is_string($custom) && $custom !== '') {
                return $custom;
            }
        }

        return $translation;
    }

    public static function siteTitle(): string
    {
        return self::resolveSiteBranding(
            'site_title',
            self::settingSiteTitle(),
            self::packagedEnglishUiBody('site_title') ?: 'Africa Health Knowledge Hub',
            self::packagedEnglishUiBody('site_title') ?: 'Africa Health Knowledge Hub'
        );
    }

    public static function siteTagline(): string
    {
        $packaged = self::packagedEnglishUiBody('site_tagline');

        // Empty slogan → no tagline on English; non-en may still use a real translation.
        return self::resolveSiteBranding(
            'site_tagline',
            self::settingSiteTagline(),
            $packaged,
            ''
        );
    }

    /**
     * Shared branding resolution for title/tagline.
     *
     * - English/default locale: non-empty admin setting wins; otherwise $emptySettingFallback.
     * - Other locales: use a real translation (differs from packaged English default); else setting; else fallback.
     */
    public static function resolveSiteBranding(
        string $uiBodyKey,
        ?string $settingValue,
        string $englishPackagedDefault,
        string $emptySettingFallback,
        ?string $locale = null
    ): string {
        $locale = $locale ?: (string) app()->getLocale();
        $setting = is_string($settingValue) ? trim($settingValue) : '';
        $isDefaultLocale = $locale === '' || $locale === 'en' || str_starts_with(strtolower($locale), 'en_');

        if ($isDefaultLocale) {
            return $setting !== '' ? $setting : $emptySettingFallback;
        }

        $translated = self::localeUiBodyValue($locale, $uiBodyKey);
        if ($translated !== '' && $translated !== $englishPackagedDefault) {
            return $translated;
        }

        if ($setting !== '') {
            return $setting;
        }

        return $emptySettingFallback;
    }

    public static function settingSiteTitle(): ?string
    {
        try {
            $settings = settings();
            foreach (['site_name', 'title'] as $key) {
                $value = $settings->{$key} ?? null;
                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        } catch (\Throwable $e) {
            // Install / missing DB.
        }

        return null;
    }

    public static function settingSiteTagline(): ?string
    {
        try {
            $value = settings()->slogan ?? null;
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        } catch (\Throwable $e) {
            // Install / missing DB.
        }

        return null;
    }

    /**
     * Packaged English string from resources/lang/en/ui_body.php (not settings overlays).
     */
    public static function packagedEnglishUiBody(string $key): string
    {
        static $packaged;

        if ($packaged === null) {
            $path = resource_path('lang/en/ui_body.php');
            $packaged = (File::exists($path) && is_array($loaded = require $path)) ? $loaded : [];
        }

        $value = $packaged[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /**
     * Locale ui_body value: storage override, then packaged lang file.
     */
    public static function localeUiBodyValue(string $locale, string $key): string
    {
        try {
            /** @var UiTranslationService $service */
            $service = app(UiTranslationService::class);

            foreach ([
                $service->storageLocalePath($locale, 'ui_body'),
                $service->resourceLocalePath($locale, 'ui_body'),
            ] as $path) {
                if (! File::exists($path)) {
                    continue;
                }
                $loaded = require $path;
                if (! is_array($loaded)) {
                    continue;
                }
                $value = $loaded[$key] ?? null;
                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during install / missing files.
        }

        return '';
    }

    public static function navCategoryTranslationKey(string $slug): string
    {
        return 'category_'.str_replace('-', '_', $slug);
    }

    public static function navStaticLinkTranslationKey(int $id): string
    {
        return 'static_link_'.$id;
    }

    public static function adminDashboardPublicationTranslationKey(int $id): string
    {
        return 'dashboard_pub_'.$id;
    }

    /**
     * Admin nav label for a dashboard publication (Language Management overrides DB title).
     *
     * @param  object{id?: int|null, title?: string|null}  $dashboard
     */
    public static function adminDashboardPublicationLabel(object $dashboard): string
    {
        $fallback = strip_tags((string) ($dashboard->title ?? ''));
        $id = (int) ($dashboard->id ?? 0);
        if ($id <= 0) {
            return $fallback;
        }

        return ui_t('admin_nav.'.self::adminDashboardPublicationTranslationKey($id), $fallback);
    }

    /**
     * @param  object{slug?: string|null, category_name?: string|null}  $category
     */
    public static function navCategoryLabel(object $category): string
    {
        $fallback = (string) ($category->category_name ?? '');
        $slug = (string) ($category->slug ?? '');
        if ($slug === '') {
            return $fallback;
        }

        // Language Management / packaged translation wins over DB category_name.
        return ui_t('frontend_nav.'.self::navCategoryTranslationKey($slug), $fallback);
    }

    /**
     * @param  object{id?: int|null, title?: string|null}  $link
     */
    public static function navStaticLinkLabel(object $link): string
    {
        $fallback = (string) ($link->title ?? '');
        $id = (int) ($link->id ?? 0);
        if ($id <= 0) {
            return $fallback;
        }

        // Language Management / packaged translation wins over DB link title.
        return ui_t('frontend_nav.'.self::navStaticLinkTranslationKey($id), $fallback);
    }

    /**
     * Flat map of group.key => translated string for client-side label refresh (no Google Translate).
     *
     * @return array<string, string>
     */
    public static function exportForCurrentLocale(): array
    {
        /** @var UiTranslationService $service */
        $service = app(UiTranslationService::class);
        $groups = config('supported_locales.ui_native_groups', [
            'frontend_nav',
            'ui_body',
            'home_sections',
        ]);

        $out = [];
        foreach ($groups as $group) {
            // Catalog includes packaged lang + dynamic DB keys (browse categories, static links).
            $catalog = $service->loadEnglishGroup($group);
            foreach ($catalog as $key => $catalogValue) {
                $fullKey = "{$group}.{$key}";
                if ($group === 'home_sections') {
                    $out[$fullKey] = self::homeSection($key);
                } elseif ($group === 'ui_body' && $key === 'site_title') {
                    $out[$fullKey] = self::siteTitle();
                } elseif ($group === 'ui_body' && $key === 'site_tagline') {
                    $out[$fullKey] = self::siteTagline();
                } else {
                    $fallback = is_string($catalogValue) ? $catalogValue : '';
                    // Prefer a real translation; otherwise use catalog (packaged + DB dynamic names).
                    // Never export the unresolved Laravel key — client JS would overwrite good labels.
                    $translated = (string) __($fullKey);
                    $resolved = ($translated !== '' && $translated !== $fullKey)
                        ? $translated
                        : $fallback;
                    if ($resolved === '' || $resolved === $fullKey) {
                        continue;
                    }
                    $out[$fullKey] = $resolved;
                }
            }
        }

        return $out;
    }
}
