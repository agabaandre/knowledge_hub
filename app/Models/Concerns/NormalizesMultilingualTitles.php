<?php

namespace App\Models\Concerns;

/**
 * Decode HTML entities and preserve UTF-8 accents/apostrophes on title-like attributes.
 *
 * @method array<string, bool> multilingualTitleAttributes()
 */
trait NormalizesMultilingualTitles
{
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (! is_string($key) || $value === null || $value === '') {
            return $value;
        }

        $fields = method_exists($this, 'multilingualTitleAttributes')
            ? $this->multilingualTitleAttributes()
            : ['title' => true];

        if (! isset($fields[$key])) {
            return $value;
        }

        return normalize_multilingual_text(is_string($value) ? $value : (string) $value);
    }

    public function setAttribute($key, $value)
    {
        $fields = method_exists($this, 'multilingualTitleAttributes')
            ? $this->multilingualTitleAttributes()
            : ['title' => true];

        if (is_string($key) && isset($fields[$key]) && $value !== null && $value !== '') {
            $value = normalize_multilingual_text(is_string($value) ? $value : (string) $value);
        }

        return parent::setAttribute($key, $value);
    }
}
