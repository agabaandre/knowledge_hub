<?php

namespace App\Services;

use App\Models\DataCategory;
use App\Models\SiteLanguage;
use App\Models\StaticLink;
use App\Support\UiLocaleLabels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UiTranslationService
{
    /**
     * Locales available in Language management (all rows in site_languages, including inactive).
     */
    public function supportedLocales(): array
    {
        return SiteLanguage::allLocaleCodes();
    }

    public function groups(): array
    {
        return config('supported_locales.ui_groups', []);
    }

    public function englishPath(string $group): string
    {
        return resource_path('lang/en/'.$group.'.php');
    }

    /**
     * Admin-saved overrides (writable). Same path the translator merges via MergingTranslationLoader.
     */
    public function storageLocalePath(string $locale, string $group): string
    {
        return storage_path('app/ui_translations/'.$locale.'/'.$group.'.php');
    }

    /**
     * Legacy path under resources (read-only on many servers).
     */
    public function resourceLocalePath(string $locale, string $group): string
    {
        return resource_path('lang/'.$locale.'/'.$group.'.php');
    }

    /**
     * @return array<string, string>
     */
    public function loadEnglishGroup(string $group): array
    {
        $path = $this->englishPath($group);
        if (! File::exists($path)) {
            return [];
        }

        $data = require $path;
        $data = is_array($data) ? $data : [];

        return array_merge($data, $this->dynamicGroupKeys($group));
    }

    /**
     * Keys discovered from the database (browse categories, key links, site branding).
     *
     * @return array<string, string>
     */
    public function dynamicGroupKeys(string $group): array
    {
        return match ($group) {
            'frontend_nav' => $this->dynamicFrontendNavKeys(),
            'ui_body' => $this->dynamicSiteBrandingKeys(),
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function dynamicFrontendNavKeys(): array
    {
        $out = [];

        try {
            if (Schema::hasTable('data_categories')) {
                foreach (DataCategory::query()->orderBy('id')->get(['slug', 'category_name']) as $category) {
                    $slug = (string) ($category->slug ?? '');
                    if ($slug === '') {
                        continue;
                    }
                    $out[UiLocaleLabels::navCategoryTranslationKey($slug)] = (string) ($category->category_name ?? '');
                }
            }

            if (Schema::hasTable('static_links')) {
                foreach (StaticLink::query()->orderBy('order')->get(['id', 'title']) as $link) {
                    $out[UiLocaleLabels::navStaticLinkTranslationKey((int) $link->id)] = (string) ($link->title ?? '');
                }
            }
        } catch (\Throwable $e) {
            // Database may be unavailable during install or early bootstrap.
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    private function dynamicSiteBrandingKeys(): array
    {
        try {
            $settings = settings();

            return array_filter([
                'site_title' => is_string($settings->site_name ?? null) && $settings->site_name !== ''
                    ? $settings->site_name
                    : (is_string($settings->title ?? null) ? $settings->title : null),
                'site_tagline' => is_string($settings->slogan ?? null) && $settings->slogan !== ''
                    ? $settings->slogan
                    : null,
            ], static fn ($value) => is_string($value) && $value !== '');
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Merged lines for editing: same keys as English, values from locale file or English fallback.
     *
     * @return array<string, string>
     */
    public function loadMergedGroup(string $locale, string $group): array
    {
        $en = $this->loadEnglishGroup($group);
        if ($en === []) {
            return [];
        }

        $loc = [];

        $storagePath = $this->storageLocalePath($locale, $group);
        if (File::exists($storagePath)) {
            $loaded = require $storagePath;
            $loc = is_array($loaded) ? $loaded : [];
        } elseif ($locale !== 'en') {
            $legacy = $this->resourceLocalePath($locale, $group);
            if (File::exists($legacy)) {
                $loaded = require $legacy;
                $loc = is_array($loaded) ? $loaded : [];
            }
        }

        $out = [];
        foreach ($en as $key => $default) {
            $out[$key] = $loc[$key] ?? $default;
        }

        return $out;
    }

    /**
     * Persist locale group; only keys present in English are stored.
     *
     * @param  array<string, string>  $submitted
     */
    public function saveGroup(string $locale, string $group, array $submitted): void
    {
        if (! in_array($locale, $this->supportedLocales(), true)) {
            throw new \InvalidArgumentException('Unsupported locale.');
        }

        if (! array_key_exists($group, $this->groups())) {
            throw new \InvalidArgumentException('Unsupported translation group.');
        }

        $en = $this->loadEnglishGroup($group);
        if ($en === []) {
            throw new \RuntimeException('English source file missing for group: '.$group);
        }

        $out = [];
        foreach (array_keys($en) as $key) {
            $val = $submitted[$key] ?? '';
            $out[$key] = is_string($val) ? $val : (string) $val;
        }

        $path = $this->storageLocalePath($locale, $group);
        $dir = dirname($path);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $export = "<?php\n\nreturn ".var_export($out, true).";\n";
        if (File::put($path, $export) === false) {
            throw new \RuntimeException('Could not write translation file: '.$path);
        }
    }
}
