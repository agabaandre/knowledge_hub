<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FederationHubCredential extends Model
{
    protected $table = 'federation_hub_credentials';

    protected $fillable = [
        'child_site_id',
        'child_name',
        'access_token_hash',
        'refresh_token_hash',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'access_token_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token_hash',
        'refresh_token_hash',
    ];
}
