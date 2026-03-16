<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('get_menu_counts')) {
    /**
     * Get cached menu counts for forums and communities
     * Falls back to live query if cache is empty
     * 
     * @param int|null $userId Optional user ID for user-specific counts
     * @return array
     */
    function get_menu_counts($userId = null)
    {
        // If no user ID provided, get totals
        if (!$userId) {
            $totalForums = Cache::remember('menu_counts:total_forums', 600, function () { // 10 minutes
                return \App\Models\Forum::where('status', 1)->count();
            });
            
            $totalCommunities = Cache::remember('menu_counts:total_communities', 600, function () { // 10 minutes
                return \App\Models\CommunityOfPractice::count();
            });
            
            return [
                'forums' => $totalForums,
                'communities' => $totalCommunities,
                'total' => $totalForums + $totalCommunities,
            ];
        }
        
        // Get user-specific counts (forums where user participated)
        $userForumsCount = Cache::remember("menu_counts:user_{$userId}:forums", 600, function () use ($userId) { // 10 minutes
            $userForumIds = \App\Models\Forum::where('created_by', $userId)->pluck('id');
            $userCommentForumIds = \App\Models\ForumComment::where('created_by', $userId)->pluck('forum_id');
            $allUserForumIds = $userForumIds->merge($userCommentForumIds)->unique();
            
            return \App\Models\Forum::whereIn('id', $allUserForumIds)
                ->where('status', 1)
                ->count();
        });
        
        // Get user's subscribed forums count (from cache or live query)
        $subscribedForumsCount = Cache::remember("menu_counts:user_{$userId}:subscribed_forums", 600, function () use ($userId) { // 10 minutes
            return \App\Models\ForumSubscription::where('user_id', $userId)->count();
        });
        
        // Get user's communities count
        $userCommunitiesCount = Cache::remember("menu_counts:user_{$userId}:communities", 600, function () use ($userId) { // 10 minutes
            return \App\Models\CommunityOfPracticeMembers::where('user_id', $userId)
                ->where('is_approved', 1)
                ->count();
        });
        
        return [
            'forums' => $userForumsCount,
            'subscribed_forums' => $subscribedForumsCount,
            'communities' => $userCommunitiesCount,
            'total' => $userForumsCount + $userCommunitiesCount,
        ];
    }
}

