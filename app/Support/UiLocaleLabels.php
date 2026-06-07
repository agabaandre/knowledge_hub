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
                $out[$fullKey] = (string) __($fullKey);
            }
        }

        return $out;
    }
}
