<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapTopologyVersionHistory extends Model
{
    protected $table = 'map_topology_version_history';

    protected $fillable = [
        'user_id',
        'from_version',
        'to_version',
        'action',
        'assignments_snapshot',
    ];

    protected $casts = [
        'assignments_snapshot' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
