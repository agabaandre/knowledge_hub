<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Answer extends Model
{
    use HasFactory;

 
    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function getAnswerExplanationAttribute($value)
    {
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c","'",'"');
        $replacements = array("", "", "", "<br>", "<br>", "\\t", "\\f", "\\b","&#39;","&#34;");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

}
