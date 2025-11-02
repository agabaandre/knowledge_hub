<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'contribution_threshold',
        'badge_color',
        'image_path',
        'sort_order',
        'is_active',
    ];

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Get badge for a given contribution count
     */
    public static function getBadgeForContributions($contributionCount)
    {
        return static::where('is_active', true)
            ->where('contribution_threshold', '<=', $contributionCount)
            ->orderBy('contribution_threshold', 'desc')
            ->first();
    }

    /**
     * Get all badges in order
     */
    public static function getAllBadgesInOrder()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('contribution_threshold', 'asc')
            ->get();
    }
}
