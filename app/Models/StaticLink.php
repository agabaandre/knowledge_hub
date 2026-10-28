<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMultilingualTitles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticLink extends Model
{
    use HasFactory;
    use NormalizesMultilingualTitles;

    protected $fillable = [
        'title', 'order', 'link', 'open_in_new_tab'
    ];

    protected function multilingualTitleAttributes(): array
    {
        return ['title' => true];
    }

    
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