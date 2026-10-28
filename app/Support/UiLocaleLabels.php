<?php

namespace App\Support;

use App\Services\UiTranslationService;

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
        $translation = __('ui_body.site_title');
        if ($translation !== 'ui_body.site_title' && $translation !== '') {
            return $translation;
        }

        return (string) (settings()->site_name ?? settings()->title ?? 'Africa Health Knowledge Hub');
    }

    public static function siteTagline(): string
    {
        $translation = __('ui_body.site_tagline');
        if ($translation !== 'ui_body.site_tagline' && $translation !== '') {
            return $translation;
        }

        return (string) (settings()->slogan ?? '');
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
            $keys = array_keys($service->loadEnglishGroup($group));
            foreach ($keys as $key) {
                $fullKey = "{$group}.{$key}";
                if ($group === 'home_sections') {
                    $out[$fullKey] = self::homeSection($key);
                } elseif ($group === 'ui_body' && $key === 'site_title') {
                    $out[$fullKey] = self::siteTitle();
                } elseif ($group === 'ui_body' && $key === 'site_tagline') {
                    $out[$fullKey] = self::siteTagline();
                } else {
                    $out[$fullKey] = (string) __($fullKey);
                }
            }
        }

        return $out;
    }
}
