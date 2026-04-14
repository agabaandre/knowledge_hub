<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RssFeed extends Model
{
    protected $fillable = [
        'name',
        'url',
        'is_active',
        'last_fetched_at',
        'last_fetch_status',
        'last_fetch_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_fetched_at' => 'datetime',
    ];

    public function stagingItems(): HasMany
    {
        return $this->hasMany(PublicationStaging::class, 'rss_feed_id');
    }
}
