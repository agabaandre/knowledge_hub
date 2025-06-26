<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubThemeticArea extends Model
{
    use HasFactory;

    protected $table="sub_thematic_area";

    public function theme(){
        return $this->belongsTo(ThemeticArea::class,"thematic_area_id","id");
    }

    public function getDescriptionAttribute($value)
    {
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c","'",'"');
        $replacements = array("", "", "", " ", " ", "\\t", "\\f", "\\b","&#39;","&#34;");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
}
