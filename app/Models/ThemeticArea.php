<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeticArea extends Model
{
    use HasFactory;
    protected $table = "thematic_area";

    public function subthemes(){
        return $this->hasMany(SubThemeticArea::class,"thematic_area_id","id");
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
