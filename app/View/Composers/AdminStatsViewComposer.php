<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\ForumComment;
use App\Models\PublicationComment;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityOfPractice;

class AdminStatsViewComposer{

    public function compose(View $view){

        // Count pending forums (not approved, status = 0)
        $pending_forums_count = Forum::where('is_approved', 0)
            ->where('status', 0)
            ->count();

        // Count pending publications (not approved)
        $pending_publications_count = Publication::where('is_approved', 0)
            ->where('is_rejected', 0)
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
            ->limit(5)
            ->get()
            ->map(function($member) {
                return [
                    'member' => $member,
                    'community' => $member->community,
                    'user' => $member->user,
                    'created_at' => $member->created_at,
                ];
            });

        // Calculate total pending count
        $total_pending_count = $pending_forums_count + $pending_publications_count + 
                              $pending_forum_comments_count + $pending_publication_comments_count +
                              $pending_cop_approvals_count;

        $data = [
            // Old variables for backward compatibility (can be removed later)
            'pending_forum_comments' => $pending_forum_comments,
            'pending_publication_comments' => $pending_publication_comments,
            'pending_publication_comments_count' => $pending_publication_comments_count,
            'pending_forum_comments_count' => $pending_forum_comments_count,
            
            // New unified notification variables
            'pending_forums_count' => $pending_forums_count,
            'pending_publications_count' => $pending_publications_count,
            'pending_cop_approvals_count' => $pending_cop_approvals_count,
            'total_pending_count' => $total_pending_count,
            'pending_forums' => $pending_forums,
            'pending_publications' => $pending_publications,
            'pending_cop_approvals' => $pendingMembers,
        ];

        $view->with($data);
    }

}
