<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\ForumComment;
use App\Models\PublicationComment;
use App\Models\CommunityOfPracticeMembers;

class NotificationController extends Controller
{
    /**
     * Get pending approval counts
     */
    public function getPendingCounts(Request $request)
    {
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

        // Calculate total pending count
        $total_pending_count = $pending_forums_count + $pending_publications_count + 
                              $pending_forum_comments_count + $pending_publication_comments_count +
                              $pending_cop_approvals_count;

        return response()->json([
            'success' => true,
            'counts' => [
                'forums' => $pending_forums_count,
                'publications' => $pending_publications_count,
                'forum_comments' => $pending_forum_comments_count,
                'publication_comments' => $pending_publication_comments_count,
                'cop_approvals' => $pending_cop_approvals_count,
                'total' => $total_pending_count,
            ],
        ]);
    }

    /**
     * Get pending items for dropdown
     */
    public function getPendingItems(Request $request)
    {
        $type = $request->input('type', 'all'); // 'all', 'forums', 'publications', 'forum_comments', 'publication_comments'
        $limit = $request->input('limit', 10);

        $items = [];

        if ($type === 'all' || $type === 'forums') {
            $forums = Forum::where('is_approved', 0)
                ->where('status', 0)
                ->with('user')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
            
            foreach ($forums as $forum) {
                $items[] = [
                    'type' => 'forum',
                    'id' => $forum->id,
                    'title' => $forum->forum_title ?? 'Untitled Forum',
                    'description' => \Illuminate\Support\Str::limit(strip_tags($forum->forum_description ?? ''), 100),
                    'author' => $forum->user->name ?? 'Unknown',
                    'created_at' => $forum->created_at ? $forum->created_at->diffForHumans() : 'N/A',
                    'url' => url('admin/forums/moderate') . '?id=' . $forum->id,
                ];
            }
        }

        if ($type === 'all' || $type === 'publications') {
            $publications = Publication::where('is_approved', 0)
                ->where('is_rejected', 0)
                ->with(['user', 'author'])
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
            
            foreach ($publications as $publication) {
                $items[] = [
                    'type' => 'publication',
                    'id' => $publication->id,
                    'title' => $publication->title ?? 'Untitled Publication',
                    'description' => \Illuminate\Support\Str::limit(strip_tags($publication->description ?? ''), 100),
                    'author' => $publication->author->name ?? ($publication->user->name ?? 'Unknown'),
                    'created_at' => $publication->created_at ? $publication->created_at->diffForHumans() : 'N/A',
                    'url' => url('admin/publications/details') . '?id=' . $publication->id,
                ];
            }
        }

        if ($type === 'all' || $type === 'forum_comments') {
            $forumComments = ForumComment::where(function($query) {
                    $query->where('status', '!=', 'approved')
                          ->orWhereNull('status');
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
            
            foreach ($forumComments as $comment) {
                $items[] = [
                    'type' => 'forum_comment',
                    'id' => $comment->id,
                    'title' => 'Forum Comment',
                    'description' => \Illuminate\Support\Str::limit(strip_tags($comment->comment ?? ''), 100),
                    'author' => $comment->user->name ?? 'Anonymous',
                    'created_at' => $comment->created_at ? $comment->created_at->diffForHumans() : 'N/A',
                    'url' => url('admin/forums/moderate'),
                ];
            }
        }

        if ($type === 'all' || $type === 'publication_comments') {
            $publicationComments = PublicationComment::where(function($query) {
                    $query->where('status', '!=', 'approved')
                          ->orWhereNull('status');
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
            
            foreach ($publicationComments as $comment) {
                $items[] = [
                    'type' => 'publication_comment',
                    'id' => $comment->id,
                    'title' => 'Publication Comment',
                    'description' => \Illuminate\Support\Str::limit(strip_tags($comment->comment ?? ''), 100),
                    'author' => $comment->user->name ?? 'Anonymous',
                    'created_at' => $comment->created_at ? $comment->created_at->diffForHumans() : 'N/A',
                    'url' => url('admin/publications/moderate'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }
}

