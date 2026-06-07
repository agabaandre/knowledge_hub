<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FederatedKnowledgeHub extends Model
{
    protected $table = 'federated_knowledge_hubs';

    protected $fillable = [
        'name',
        'base_url',
        'remote_site_id',
        'api_token',
        'api_refresh_token',
        'api_token_expires_at',
        'mapped_country_id',
        'is_active',
        'auto_sync',
        'connection_status',
        'connection_error',
        'last_synced_at',
        'last_manifest',
        'cached_public_data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_sync' => 'boolean',
        'last_synced_at' => 'datetime',
        'api_token_expires_at' => 'datetime',
        'last_manifest' => 'array',
        'cached_public_data' => 'array',
    ];

    protected $hidden = [
        'api_token',
        'api_refresh_token',
    ];

    public function mappedCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'mapped_country_id');
    }

    public function normalizedBaseUrl(): string
    {
        return rtrim((string) $this->base_url, '/');
    }
}
