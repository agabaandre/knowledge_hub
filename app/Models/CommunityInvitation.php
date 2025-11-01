<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommunityInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_of_practice_id',
        'email',
        'token',
        'invited_by',
        'responded_at',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Generate a unique token for the invitation
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if invitation has been responded to
     */
    public function isResponded(): bool
    {
        return !is_null($this->responded_at);
    }

    /**
     * Check if invitation is valid (not expired and not responded)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isResponded();
    }

    /**
     * Mark invitation as responded
     */
    public function markAsResponded(): void
    {
        $this->update(['responded_at' => now()]);
    }

    /**
     * Relationships
     */
    public function community()
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
