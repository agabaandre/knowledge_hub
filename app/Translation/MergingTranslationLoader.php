<?php

namespace App\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

/**
 * Loads PHP lang groups from resources/lang, then merges overrides from
 * storage/app/ui_translations/{locale}/{group}.php (writable on production).
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

        $overrideFile = $this->overrideBasePath.'/'.$locale.'/'.$group.'.php';
        if ($this->files->exists($overrideFile)) {
            $extra = $this->files->getRequire($overrideFile);
            if (is_array($extra)) {
                return array_merge($base, $extra);
            }
        }

        return $base;
    }
}
