<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ForumEngagement extends Model
{
    use HasFactory;

    protected $table = 'forum_engagements';
    
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'forum_posts',
        'forum_comments',
    ];

    public $timestamps = true;

    /**
     * Get the user that owns the engagement record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment forum posts for a user in the current month/year.
     */
    public static function incrementForumPost($userId)
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $engagement = static::firstOrNew([
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
        ]);

        $engagement->forum_posts = ($engagement->forum_posts ?? 0) + 1;
        $engagement->save();

        return $engagement;
    }

    /**
     * Increment forum comments for a user in the current month/year.
     */
    public static function incrementForumComment($userId)
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $engagement = static::firstOrNew([
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
        ]);

        $engagement->forum_comments = ($engagement->forum_comments ?? 0) + 1;
        $engagement->save();

        return $engagement;
    }

    /**
     * Get total forum posts for a user.
     */
    public static function getTotalForumPosts($userId)
    {
        return static::where('user_id', $userId)
            ->sum('forum_posts');
    }

    /**
     * Get total forum comments for a user.
     */
    public static function getTotalForumComments($userId)
    {
        return static::where('user_id', $userId)
            ->sum('forum_comments');
    }

    /**
     * Get total engagements (posts + comments) for a user.
     */
    public static function getTotalEngagements($userId)
    {
        return static::where('user_id', $userId)
            ->sum(DB::raw('forum_posts + forum_comments'));
    }
}

