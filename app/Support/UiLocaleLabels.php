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

    /**
     * @param  object{slug?: string|null, category_name?: string|null}  $category
     */
    public static function navCategoryLabel(object $category): string
    {
        $slug = (string) ($category->slug ?? '');
        if ($slug === '') {
            return (string) ($category->category_name ?? '');
        }

        $fullKey = 'frontend_nav.'.self::navCategoryTranslationKey($slug);
        $translation = __($fullKey);

        return $translation !== $fullKey ? $translation : (string) ($category->category_name ?? '');
    }

    /**
     * @param  object{id?: int|null, title?: string|null}  $link
     */
    public static function navStaticLinkLabel(object $link): string
    {
        $id = (int) ($link->id ?? 0);
        if ($id <= 0) {
            return (string) ($link->title ?? '');
        }

        $fullKey = 'frontend_nav.'.self::navStaticLinkTranslationKey($id);
        $translation = __($fullKey);

        return $translation !== $fullKey ? $translation : (string) ($link->title ?? '');
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
