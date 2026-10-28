<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Answer extends Model
{
    use HasFactory;

    public $timestamps = false;

 
    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function getAnswerExplanationAttribute($value)
    {
        return normalize_multiline_display_text(is_string($value) ? $value : (string) $value);
    }

}
