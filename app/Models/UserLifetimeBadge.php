<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLifetimeBadge extends Model
{
    protected $fillable = [
        'user_id',
        'badge_type_id',
        'lifetime_contributions',
        'last_upgraded_at',
    ];

    protected $casts = [
        'last_upgraded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badgeType(): BelongsTo
    {
        return $this->belongsTo(BadgeType::class);
    }
}
