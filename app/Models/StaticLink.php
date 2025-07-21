<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'order', 'link', 'open_in_new_tab'
    ];

    
    protected static function booted()
    {
        static::created(function ($static_link) {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::updated(function ($static_link) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }
} 