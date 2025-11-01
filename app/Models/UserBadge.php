<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'community_of_practice_id',
        'badge_type_id',
        'year',
        'month',
        'contributions_count',
        'awarded_at',
        'email_sent',
    ];

    protected $dates = [
        'awarded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }

    public function badgeType()
    {
        return $this->belongsTo(BadgeType::class);
    }

    /**
     * Get user's badges for a specific community
     */
    public static function getUserBadgesForCommunity($userId, $communityId)
    {
        return static::where('user_id', $userId)
            ->where('community_of_practice_id', $communityId)
            ->with('badgeType')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('badge_type_id', 'asc')
            ->get();
    }

    /**
     * Check if user has a specific badge for a community in a given month/year
     */
    public static function hasBadge($userId, $communityId, $badgeTypeId, $year, $month)
    {
        return static::where('user_id', $userId)
            ->where('community_of_practice_id', $communityId)
            ->where('badge_type_id', $badgeTypeId)
            ->where('year', $year)
            ->where('month', $month)
            ->exists();
    }
}
