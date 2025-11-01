<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumEngagement;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing forum posts and comments to monthly engagement table
        try {
            $forums = Forum::whereNotNull('created_by')->get();
            
            foreach ($forums as $forum) {
                if ($forum->created_by) {
                    // Try to get created_at, fallback to now if not available
                    try {
                        $createdAt = $forum->created_at ?? now();
                        if (!$createdAt instanceof \Carbon\Carbon && !$createdAt instanceof \DateTime) {
                            $createdAt = now();
                        }
                    } catch (\Exception $e) {
                        $createdAt = now();
                    }
                    
                    $year = $createdAt->year ?? now()->year;
                    $month = $createdAt->month ?? now()->month;
                    
                    ForumEngagement::firstOrCreate(
                        [
                            'user_id' => $forum->created_by,
                            'year' => $year,
                            'month' => $month,
                        ],
                        [
                            'forum_posts' => 0,
                            'forum_comments' => 0,
                        ]
                    );
                    
                    // Increment forum posts count
                    ForumEngagement::where('user_id', $forum->created_by)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->increment('forum_posts');
                }
            }
            
            // Migrate existing forum comments
            // Note: ForumComment doesn't have timestamps, so we'll use current month/year
            $comments = ForumComment::whereNotNull('created_by')->get();
            
            foreach ($comments as $comment) {
                if ($comment->created_by) {
                    $now = now();
                    $year = $now->year;
                    $month = $now->month;
                    
                    ForumEngagement::firstOrCreate(
                        [
                            'user_id' => $comment->created_by,
                            'year' => $year,
                            'month' => $month,
                        ],
                        [
                            'forum_posts' => 0,
                            'forum_comments' => 0,
                        ]
                    );
                    
                    // Increment forum comments count
                    ForumEngagement::where('user_id', $comment->created_by)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->increment('forum_comments');
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail migration
            \Log::warning('Error populating forum engagements: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear forum engagement data
        // ForumEngagement::truncate();
    }
};

