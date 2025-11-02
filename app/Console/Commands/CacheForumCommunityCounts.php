<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumSubscription;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheForumCommunityCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:forum-community-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache forum and community counts for all users (runs every 5 minutes)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Caching forum and community counts...');
        
        try {
            // Cache total counts (for non-logged-in users)
            $totalForums = Forum::where('status', 1)->count();
            $totalCommunities = CommunityOfPractice::count();
            
            Cache::put('menu_counts:total_forums', $totalForums, 600); // 10 minutes in seconds
            Cache::put('menu_counts:total_communities', $totalCommunities, 600);
            
            $this->info("Cached total forums: {$totalForums}, total communities: {$totalCommunities}");
            
            // Cache user-specific counts for active users (limit to avoid memory issues)
            // Get unique user IDs from forums, comments, and community memberships
            $forumUserIds = DB::table('forums')
                ->where('status', 1)
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by');
            
            $commentUserIds = DB::table('forum_comments')
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by');
            
            $communityUserIds = DB::table('community_of_practice_members')
                ->where('is_approved', 1)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');
            
            $subscribedUserIds = DB::table('forum_subscriptions')
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');
            
            $allUserIds = $forumUserIds->merge($commentUserIds)->merge($communityUserIds)->merge($subscribedUserIds)->unique()->take(500);
            
            $cachedUsers = 0;
            foreach ($allUserIds as $userId) {
                try {
                    // Get user's forums count (forums where user participated - created or commented)
                    $userForumIds = Forum::where('created_by', $userId)->pluck('id');
                    $userCommentForumIds = ForumComment::where('created_by', $userId)->pluck('forum_id');
                    $allUserForumIds = $userForumIds->merge($userCommentForumIds)->unique();
                    $userForumsCount = $allUserForumIds->isEmpty() ? 0 : Forum::whereIn('id', $allUserForumIds)
                        ->where('status', 1)
                        ->count();
                    
                    // Get user's subscribed forums count
                    $subscribedForumsCount = ForumSubscription::where('user_id', $userId)->count();
                    
                    // Get user's communities count
                    $userCommunitiesCount = CommunityOfPracticeMembers::where('user_id', $userId)
                        ->where('is_approved', 1)
                        ->count();
                    
                    // Cache user-specific counts
                    Cache::put("menu_counts:user_{$userId}:forums", $userForumsCount, 600);
                    Cache::put("menu_counts:user_{$userId}:subscribed_forums", $subscribedForumsCount, 600);
                    Cache::put("menu_counts:user_{$userId}:communities", $userCommunitiesCount, 600);
                    
                    $cachedUsers++;
                } catch (\Exception $e) {
                    // Skip individual user errors and continue
                    continue;
                }
            }
            
            $this->info("Cached counts for {$cachedUsers} users.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error caching counts: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

