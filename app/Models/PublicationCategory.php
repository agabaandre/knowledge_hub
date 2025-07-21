<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationCategory extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($publication) {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::updated(function ($publication) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

}
