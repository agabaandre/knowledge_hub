<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Link between a user and a provider subject (Google sub / Microsoft oid /
 * LinkedIn member id). Matching on this, not on email, is what stops an
 * attacker logging in as an arbitrary account.
 */
class SocialAccount extends Model
{
    protected $fillable = ['user_id', 'provider', 'provider_user_id', 'email', 'last_login_at'];

    protected $casts = ['last_login_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
