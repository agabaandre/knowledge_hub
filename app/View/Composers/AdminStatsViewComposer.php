<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\ForumComment;
use App\Models\PublicationComment;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityOfPractice;
use App\Models\ContentRequest;

class AdminStatsViewComposer{

    public function compose(View $view){

        // Count forums awaiting approval (excludes rejected)
        $pending_forums_count = Forum::pendingApproval()->count();

        // Count pending publications (not approved)
        $pending_publications_count = Publication::where('is_approved', 0)
            ->where('is_rejected', 0)
            ->count();

        $rejected_publications_count = Publication::where('is_version', 0)
            ->where('is_rejected', 1)
            ->count();

        // Count pending forum comments (not approved - excluding 'approved' status)
        $pending_forum_comments_count = ForumComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->count();

        // Count pending publication comments (not approved - excluding 'approved' status)
        $pending_publication_comments_count = PublicationComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->count();

        // Count pending COP member approvals
        $pending_cop_approvals_count = CommunityOfPracticeMembers::where('is_approved', 0)
            ->count();

        $pending_federated_content_count = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('federated_content_items')) {
            $pending_federated_content_count = \App\Models\FederatedContentItem::query()->pendingReview()->count();
        }

        // Content requests: unprocessed and processed counters for admin navigation
        $pending_content_requests_count = 0;
        $processed_content_requests_count = 0;
        $pending_content_requests = collect();
        if (auth()->check() && auth()->user()->can('view_content_requests')) {
            $pending_content_requests_count = ContentRequest::whereNull('processed_at')->count();
            $processed_content_requests_count = ContentRequest::whereNotNull('processed_at')->count();
            $pending_content_requests = ContentRequest::whereNull('processed_at')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        // Get recent pending forum comments (not approved - for dropdown)
        // Note: ForumComment uses 'created_by' for user relationship
        $pending_forum_comments = ForumComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent pending publication comments (not approved - for dropdown)
        $pending_publication_comments = PublicationComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent pending forums (for dropdown)
        $pending_forums = Forum::where('is_approved', 0)
            ->where('status', 0)
            ->with('user')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Get recent pending publications (for dropdown)
        $pending_publications = Publication::where('is_approved', 0)
            ->where('is_rejected', 0)
            ->with(['user', 'author'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Get communities with pending member approvals (for dropdown)
        $pendingMembers = CommunityOfPracticeMembers::where('is_approved', 0)
            ->with(['community', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($member) {
                // Ensure community relationship is loaded, if null try to reload
                if (!$member->community && $member->community_of_practice_id) {
                    $member->load('community');
                }
                // If still null, try to fetch directly
                if (!$member->community && $member->community_of_practice_id) {
                    $member->community = \App\Models\CommunityOfPractice::find($member->community_of_practice_id);
                }
                return [
                    'member' => $member,
                    'community' => $member->community,
                    'user' => $member->user,
                    'created_at' => $member->created_at,
                    'type' => 'cop_approval',
                ];
            })
            ->filter(function($approval) {
                // Filter out approvals where community doesn't exist
                return $approval['community'] !== null && $approval['user'] !== null;
            });

        // Combine all notifications into a single sorted list by creation date
        $allNotifications = collect();
        
        // Add forums
        foreach ($pending_forums as $forum) {
            $allNotifications->push([
                'type' => 'forum',
                'item' => $forum,
                'created_at' => $forum->created_at,
            ]);
        }
        
        // Add publications
        foreach ($pending_publications as $publication) {
            $allNotifications->push([
                'type' => 'publication',
                'item' => $publication,
                'created_at' => $publication->created_at,
            ]);
        }
        
        // Add forum comments
        foreach ($pending_forum_comments as $comment) {
            $allNotifications->push([
                'type' => 'forum_comment',
                'item' => $comment,
                'created_at' => $comment->created_at,
            ]);
        }
        
        // Add publication comments
        foreach ($pending_publication_comments as $comment) {
            $allNotifications->push([
                'type' => 'publication_comment',
                'item' => $comment,
                'created_at' => $comment->created_at,
            ]);
        }
        
        // Add COP approvals
        foreach ($pendingMembers as $approval) {
            $allNotifications->push([
                'type' => 'cop_approval',
                'item' => $approval,
                'created_at' => $approval['created_at'],
            ]);
        }

        // Content requests (pending / not processed)
        foreach ($pending_content_requests as $cr) {
            $allNotifications->push([
                'type' => 'content_request',
                'item' => $cr,
                'created_at' => $cr->created_at,
            ]);
        }
        
        // Sort by created_at descending (most recent first) and take top 10
        $sortedNotifications = $allNotifications->sortByDesc('created_at')->take(10)->values();

        // Calculate total pending count
        $total_pending_count = $pending_forums_count + $pending_publications_count +
                              $pending_forum_comments_count + $pending_publication_comments_count +
                              $pending_cop_approvals_count + $pending_content_requests_count +
                              $pending_federated_content_count;

        $data = [
            // Old variables for backward compatibility (can be removed later)
            'pending_forum_comments' => $pending_forum_comments,
            'pending_publication_comments' => $pending_publication_comments,
            'pending_publication_comments_count' => $pending_publication_comments_count,
            'pending_forum_comments_count' => $pending_forum_comments_count,
            
            // New unified notification variables
            'pending_forums_count' => $pending_forums_count,
            'pending_publications_count' => $pending_publications_count,
            'rejected_publications_count' => $rejected_publications_count,
            'pending_cop_approvals_count' => $pending_cop_approvals_count,
            'pending_federated_content_count' => $pending_federated_content_count,
            'pending_content_requests_count' => $pending_content_requests_count,
            'processed_content_requests_count' => $processed_content_requests_count,
            'total_pending_count' => $total_pending_count,
            'pending_forums' => $pending_forums,
            'pending_publications' => $pending_publications,
            'pending_cop_approvals' => $pendingMembers,
            'sorted_notifications' => $sortedNotifications, // New unified sorted list
        ];

        $view->with($data);
    }

}
