<?php

namespace App\Translation;

use App\Services\UiTranslationService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

/**
 * Loads PHP lang groups from resources/lang, then merges overrides from
 * storage/app/ui_translations/{locale}/{group}.php (writable on production).
 *
 * For non-English locales, storage values that are empty or still identical to
 * English do not replace a real packaged translation — so nav can follow the
 * site default locale while content remains authored in English.
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

        $base = $this->loadPath($this->path, $locale, $group);
        if (! is_array($base)) {
            $base = [];
        }

        $base = array_merge($base, app(UiTranslationService::class)->dynamicGroupKeys($group));

        $overrideFile = $this->overrideBasePath.'/'.$locale.'/'.$group.'.php';
        if (! $this->files->exists($overrideFile)) {
            return $base;
        }

        $extra = $this->files->getRequire($overrideFile);
        if (! is_array($extra)) {
            return $base;
        }

        $english = [];
        if ($locale !== 'en' && $locale !== 'en_US') {
            $english = $this->loadPath($this->path, 'en', $group);
            if (! is_array($english)) {
                $english = [];
            }
        }

        foreach ($extra as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $enVal = $english[$key] ?? null;
            $baseVal = $base[$key] ?? null;

            // Keep packaged locale string when the admin override is still English.
            if ($enVal !== null
                && $value === $enVal
                && is_string($baseVal)
                && $baseVal !== ''
                && $baseVal !== $enVal
            ) {
                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }
}
