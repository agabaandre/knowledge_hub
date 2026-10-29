<?php

namespace App\Translation;

use App\Services\UiTranslationService;
use App\Support\UiTranslationCache;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

/**
 * Loads PHP lang groups with this priority (last wins):
 * 1. English packaged defaults
 * 2. Dynamic DB seeds (browse categories, key links, branding) — fill missing keys only
 * 3. Locale packaged resources/lang/{locale}
 * 4. Language Management storage overrides (non-empty) — always win
 *
 * So Configure → Language management controls nav and UI even when packaged
 * or hardcoded fallbacks exist.
 */
class MergingTranslationLoader extends FileLoader
{
    /** @var string */
    protected $overrideBasePath;

    public function __construct(Filesystem $files, $path, $overrideBasePath)
    {
        parent::__construct($files, $path);
        $this->overrideBasePath = rtrim((string) $overrideBasePath, '/');
    }

    /**
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return parent::load($locale, $group, $namespace);
        }

        if (! is_null($namespace) && $namespace !== '*') {
            return parent::load($locale, $group, $namespace);
        }

        $cached = UiTranslationCache::getGroup((string) $locale, (string) $group);
        if ($cached !== null) {
            return $cached;
        }

        $english = $this->loadPath($this->path, 'en', $group);
        if (! is_array($english)) {
            $english = [];
        }

        $localePackaged = [];
        if ($locale !== 'en' && $locale !== 'en_US') {
            $loaded = $this->loadPath($this->path, $locale, $group);
            $localePackaged = is_array($loaded) ? $loaded : [];
        } else {
            $localePackaged = $english;
        }

        $dynamic = [];
        try {
            $dynamic = app(UiTranslationService::class)->dynamicGroupKeys($group);
            if (! is_array($dynamic)) {
                $dynamic = [];
            }
        } catch (\Throwable $e) {
            $dynamic = [];
        }

        // English → DB seeds for missing keys → packaged locale (packaged beats English DB seeds).
        $base = array_replace($english, $dynamic, $localePackaged);

        $overrideFile = $this->overrideBasePath.'/'.$locale.'/'.$group.'.php';
        if ($this->files->exists($overrideFile)) {
            $extra = $this->files->getRequire($overrideFile);
            if (is_array($extra)) {
                foreach ($extra as $key => $value) {
                    if (! is_string($key)) {
                        continue;
                    }
                    // Non-empty Language Management values override packaged + DB seeds…
                    if (! is_string($value) || trim($value) === '') {
                        continue;
                    }

                    // …except English stubs left by failed AI/copy. Those must not beat a
                    // real packaged translation (common FR production failure: PT/AR OK, FR English).
                    $englishVal = $english[$key] ?? null;
                    $packagedVal = $localePackaged[$key] ?? null;
                    if (is_string($englishVal)
                        && $value === $englishVal
                        && is_string($packagedVal)
                        && trim($packagedVal) !== ''
                        && $packagedVal !== $englishVal
                    ) {
                        continue;
                    }

                    $base[$key] = $value;
                }
            }
        }

        UiTranslationCache::putGroup((string) $locale, (string) $group, $base);

        return $base;
    }
}
