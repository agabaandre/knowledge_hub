<?php

namespace App\Services;

use App\Models\SiteLanguage;
use Illuminate\Support\Facades\File;

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

    public function localePath(string $locale, string $group): string
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

        return is_array($data) ? $data : [];
    }

    /**
     * Merged lines for editing: same keys as English, values from locale file or English fallback.
     *
     * @return array<string, string>
     */
    public function loadMergedGroup(string $locale, string $group): array
    {
        $en = $this->loadEnglishGroup($group);
        if ($locale === 'en') {
            return $en;
        }

        $path = $this->localePath($locale, $group);
        $loc = File::exists($path) ? require $path : [];
        $loc = is_array($loc) ? $loc : [];

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

        $dir = resource_path('lang/'.$locale);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $path = $this->localePath($locale, $group);
        $export = "<?php\n\nreturn ".var_export($out, true).";\n";
        File::put($path, $export);
    }
}
