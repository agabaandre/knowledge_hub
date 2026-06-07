<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapDefinition extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'description',
        'provider',
        'source_type',
        'topology_preset',
        'topology_url',
        'collection_version',
        'map_key',
        'script_path',
        'join_by',
        'iso_property',
        'scope',
        'country_iso2',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
