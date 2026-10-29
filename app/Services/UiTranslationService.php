<?php

namespace App\Services;

use App\Models\DataCategory;
use App\Models\SiteLanguage;
use App\Models\StaticLink;
use App\Support\UiLocaleLabels;
use App\Support\UiTranslationCache;
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

        // Language management edits flat string keys only (skip nested Laravel arrays like validation.custom).
        $flat = [];
        foreach ($data as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $flat[$key] = $value;
            }
        }

        return array_merge($flat, $this->dynamicGroupKeys($group));
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
            'admin_nav' => $this->dynamicAdminNavKeys(),
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
     * Admin-nav dashboard publication titles (same set as DashboardsViewComposer).
     *
     * @return array<string, string>
     */
    private function dynamicAdminNavKeys(): array
    {
        $out = [];

        try {
            if (! Schema::hasTable('publications')) {
                return $out;
            }

            $query = \App\Models\Publication::query()
                ->where(function ($q) {
                    $q->whereHas('data_category', function ($dq) {
                        $dq->where('is_dashboard', 1);
                    })->orWhere('is_admin_only_access', 1);
                })
                ->orderBy('id')
                ->get(['id', 'title']);

            foreach ($query as $dashboard) {
                $id = (int) $dashboard->id;
                if ($id <= 0) {
                    continue;
                }
                $out[UiLocaleLabels::adminDashboardPublicationTranslationKey($id)] = strip_tags((string) ($dashboard->title ?? ''));
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
     * Merged lines for editing: English keys are the catalog.
     * Priority: non-empty Language Management (storage) → packaged lang → English.
     * Admin/storage overrides always win so editors see what the live site will use.
     *
     * @return array<string, string>
     */
    public function loadMergedGroup(string $locale, string $group): array
    {
        $cacheKeyLocale = $locale.'#merged';
        $cached = UiTranslationCache::getGroup($cacheKeyLocale, $group);
        if ($cached !== null) {
            return $cached;
        }

        $en = $this->loadEnglishGroup($group);
        if ($en === []) {
            return [];
        }

        $packaged = [];
        if ($locale !== 'en') {
            $legacy = $this->resourceLocalePath($locale, $group);
            if (File::exists($legacy)) {
                $loaded = require $legacy;
                $packaged = is_array($loaded) ? $loaded : [];
            }
        }

        $storage = [];
        $storagePath = $this->storageLocalePath($locale, $group);
        if (File::exists($storagePath)) {
            $loaded = require $storagePath;
            $storage = is_array($loaded) ? $loaded : [];
        }

        $out = [];
        foreach ($en as $key => $default) {
            $storageVal = $storage[$key] ?? null;
            $packagedVal = $packaged[$key] ?? null;

            if (is_string($storageVal) && trim($storageVal) !== '') {
                // Ignore English storage stubs when packaged locale already has a real translation.
                $isEnglishStub = $storageVal === $default
                    && is_string($packagedVal)
                    && trim($packagedVal) !== ''
                    && $packagedVal !== $default;
                if (! $isEnglishStub) {
                    $out[$key] = $storageVal;
                    continue;
                }
            }

            if (is_string($packagedVal) && trim($packagedVal) !== '') {
                $out[$key] = $packagedVal;
                continue;
            }

            $out[$key] = $default;
        }

        UiTranslationCache::putGroup($cacheKeyLocale, $group, $out);

        return $out;
    }

    /**
     * Drop AI/copy values that are empty or still identical to English so they cannot
     * override packaged locale files (common production failure after AI fallback).
     *
     * @param  array<string, string>  $partial
     * @return array<string, string>
     */
    public function rejectEnglishIdentical(string $group, array $partial): array
    {
        $en = $this->loadEnglishGroup($group);
        $out = [];
        foreach ($partial as $key => $value) {
            if (! is_string($key) || ! array_key_exists($key, $en)) {
                continue;
            }
            if (! is_string($value)) {
                $value = (string) $value;
            }
            $value = trim($value);
            if ($value === '') {
                continue;
            }
            if ($value === $en[$key]) {
                continue;
            }
            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * Merge partial translations into the stored locale group without wiping other keys.
     *
     * @param  array<string, string>  $partial
     * @return int Number of keys written from $partial
     */
    public function mergeGroupTranslations(string $locale, string $group, array $partial, bool $skipEnglishIdentical = true): int
    {
        $en = $this->loadEnglishGroup($group);
        if ($en === []) {
            throw new \RuntimeException('English source file missing for group: '.$group);
        }

        if ($skipEnglishIdentical) {
            $partial = $this->rejectEnglishIdentical($group, $partial);
        }

        $raw = $this->loadRawLocaleGroup($locale, $group);
        $out = [];
        foreach (array_keys($en) as $key) {
            $existing = $raw[$key] ?? '';
            $out[$key] = is_string($existing) ? $existing : (string) $existing;
        }

        $written = 0;
        foreach ($partial as $key => $value) {
            if (! array_key_exists($key, $en)) {
                continue;
            }
            if (! is_string($value)) {
                $value = (string) $value;
            }
            if (trim($value) === '') {
                continue;
            }
            $out[$key] = $value;
            $written++;
        }

        if ($written === 0) {
            return 0;
        }

        $this->saveGroup($locale, $group, $out);

        return $written;
    }

    /**
     * Remove Language Management storage values that are still English (or empty).
     * Packaged locale files then show again — fixes online sites where AI fallback
     * wrote English stubs that overrode good packaged French/etc.
     *
     * @return array{cleared: int, files_updated: int, files_deleted: int}
     */
    public function clearEnglishOverrides(string $locale, ?string $group = null): array
    {
        if ($locale === 'en') {
            return ['cleared' => 0, 'files_updated' => 0, 'files_deleted' => 0];
        }

        $groups = $group === null || $group === ''
            ? array_keys($this->groups())
            : [$group];

        $cleared = 0;
        $updated = 0;
        $deleted = 0;

        foreach ($groups as $name) {
            if (! array_key_exists($name, $this->groups()) && $group !== null) {
                continue;
            }

            $path = $this->storageLocalePath($locale, $name);
            if (! File::exists($path)) {
                continue;
            }

            $en = $this->loadEnglishGroup($name);
            $raw = $this->loadRawLocaleGroup($locale, $name);
            $kept = [];
            $removed = 0;

            foreach ($raw as $key => $value) {
                if (! is_string($key)) {
                    continue;
                }
                if (! is_string($value)) {
                    $value = (string) $value;
                }
                $trim = trim($value);
                if ($trim === '') {
                    $removed++;
                    continue;
                }
                if (array_key_exists($key, $en) && $trim === $en[$key]) {
                    $removed++;
                    continue;
                }
                $kept[$key] = $value;
            }

            if ($removed === 0) {
                continue;
            }

            $cleared += $removed;

            if ($kept === []) {
                File::delete($path);
                $deleted++;
            } else {
                $dir = dirname($path);
                if (! File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
                $export = "<?php\n\nreturn ".var_export($kept, true).";\n";
                if (File::put($path, $export) === false) {
                    throw new \RuntimeException('Could not write translation file: '.$path);
                }
                if (function_exists('opcache_invalidate')) {
                    @opcache_invalidate($path, true);
                }
                $updated++;
            }

            UiTranslationCache::invalidateAfterSave($locale, $name);
        }

        UiTranslationCache::flushAll();
        try {
            app('translator')->setLoaded([]);
        } catch (\Throwable $e) {
            // ignore
        }

        return [
            'cleared' => $cleared,
            'files_updated' => $updated,
            'files_deleted' => $deleted,
        ];
    }

    /**
     * Keys that still need translation (empty or still identical to English).
     *
     * @param  list<string>|null  $onlyKeys
     * @return array<string, string> English source keyed by translation key
     */
    public function missingKeysForLocale(string $locale, string $group, ?array $onlyKeys = null): array
    {
        $english = $this->loadEnglishGroup($group);
        if ($english === []) {
            return [];
        }

        if (is_array($onlyKeys) && $onlyKeys !== []) {
            $subset = [];
            foreach ($onlyKeys as $key) {
                if (array_key_exists($key, $english)) {
                    $subset[$key] = $english[$key];
                }
            }
            $english = $subset;
        }

        $merged = $this->loadMergedGroup($locale, $group);
        $missing = [];
        foreach ($english as $key => $enValue) {
            $current = trim((string) ($merged[$key] ?? ''));
            if ($current === '' || $current === $enValue) {
                $missing[$key] = $enValue;
            }
        }

        return $missing;
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

        // Ensure next __()/ui_t() call picks up Language Management overrides (nav included).
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($path, true);
        }
        // Bust Redis (or file-cache) entries for runtime __()/ui_t() and Language Management grids.
        UiTranslationCache::invalidateAfterSave($locale, $group);
        try {
            app('translator')->setLoaded([]);
        } catch (\Throwable $e) {
            // Translator may be unbound in some console contexts.
        }
    }

    /**
     * Raw locale values from storage or committed resource files (no English fill).
     *
     * @return array<string, string>
     */
    public function loadRawLocaleGroup(string $locale, string $group): array
    {
        $storagePath = $this->storageLocalePath($locale, $group);
        if (File::exists($storagePath)) {
            $loaded = require $storagePath;

            return is_array($loaded) ? $loaded : [];
        }

        if ($locale === 'en') {
            return $this->loadEnglishGroup($group);
        }

        $legacy = $this->resourceLocalePath($locale, $group);
        if (File::exists($legacy)) {
            $loaded = require $legacy;

            return is_array($loaded) ? $loaded : [];
        }

        return [];
    }

    /**
     * Copy English source strings into a locale payload.
     *
     * @param  array<string, string>  $existing
     * @return array<string, string>
     */
    public function copyEnglishPayload(string $locale, string $group, array $existing, bool $onlyEmpty = true): array
    {
        $english = $this->loadEnglishGroup($group);
        $out = [];

        foreach ($english as $key => $value) {
            $current = $existing[$key] ?? '';
            if ($onlyEmpty && is_string($current) && trim($current) !== '') {
                $out[$key] = $current;
            } else {
                $out[$key] = is_string($value) ? $value : (string) $value;
            }
        }

        return $out;
    }

    /**
     * Persist English defaults into storage for one group or every editable group.
     */
    public function copyEnglishToLocale(string $locale, ?string $group = null, bool $onlyEmpty = true): int
    {
        $groups = $group === null || $group === ''
            ? array_keys($this->groups())
            : [$group];

        $written = 0;
        foreach ($groups as $name) {
            $existing = $onlyEmpty ? $this->loadRawLocaleGroup($locale, $name) : [];
            $payload = $this->copyEnglishPayload($locale, $name, $existing, $onlyEmpty);
            $this->saveGroup($locale, $name, $payload);
            $written += count($payload);
        }

        return $written;
    }
}
