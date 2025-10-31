<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    protected $fillable = [
        'user_id',
        'subtheme_id',
    ];

    public function subtheme(){
        return $this->belongsTo(SubThemeticArea::class,"subtheme_id","id");
    }
}
