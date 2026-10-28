<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubThemeticArea extends Model
{
    use HasFactory;

    protected $table="sub_thematic_area";

    public const DEFAULT_ICON = 'fa-check-circle';

    public function theme(){
        return $this->belongsTo(ThemeticArea::class,"thematic_area_id","id");
    }

    public function getIconAttribute($value): string
    {
        return self::normalizeIcon($value);
    }

    public function setIconAttribute($value): void
    {
        $this->attributes['icon'] = self::normalizeIcon($value);
    }

    /**
     * Empty / accidental Select2 first-option values fall back to the circle icon.
     */
    public static function normalizeIcon(mixed $value): string
    {
        $icon = trim((string) ($value ?? ''));
        if (str_starts_with($icon, 'fa fa-')) {
            $icon = substr($icon, 3);
        }
        if ($icon === '' || $icon === 'fa' || $icon === 'fa-ad') {
            return self::DEFAULT_ICON;
        }

        return $icon;
    }

    public function getDescriptionAttribute($value)
    {
        return normalize_multilingual_text(is_string($value) ? $value : (string) $value);
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = normalize_multilingual_text(is_string($value) ? $value : (string) $value);
    }

    public function getDetailedDescriptionAttribute($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return normalize_multilingual_text(is_string($value) ? $value : (string) $value);
    }

    public function setDetailedDescriptionAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['detailed_description'] = null;

            return;
        }

        $this->attributes['detailed_description'] = normalize_multilingual_text(is_string($value) ? $value : (string) $value);
    }
}
