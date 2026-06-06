<?php

namespace App\Support;

use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Single source of truth for what counts as a hub contribution toward badges and profile stats.
 *
 * Only approved, active, non-rejected content currently on the hub is included.
 */
class ContributorContributions
{
    public static function authorIdForUser(int $userId): ?int
    {
        $authorId = User::query()->whereKey($userId)->value('author_id');

        return $authorId ? (int) $authorId : null;
    }

    public static function eligiblePublicationsQuery(?int $userId = null, ?int $authorId = null): Builder
    {
        $query = Publication::query()
            ->where('is_version', 0)
            ->where('is_approved', 1)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            })
            ->where('is_active', 'Active');

        if ($userId || $authorId) {
            $query->where(function ($q) use ($userId, $authorId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                }
                if ($authorId) {
                    $userId ? $q->orWhere('author_id', $authorId) : $q->where('author_id', $authorId);
                }
            });
        }

        return $query;
    }

    public static function eligibleForumsQuery(int $userId): Builder
    {
        return Forum::query()
            ->where('created_by', $userId)
            ->where('status', 1)
            ->where('is_approved', 1)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            });
    }

    public static function eligibleForumCommentsQuery(int $userId): Builder
    {
        return ForumComment::query()
            ->where('created_by', $userId)
            ->whereHas('forum', function ($q) {
                $q->where('status', 1)
                    ->where('is_approved', 1)
                    ->where(function ($q) {
                        $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                    });
            });
    }

    public static function countLifetimeForUser(int $userId): int
    {
        $authorId = self::authorIdForUser($userId);

        return self::eligiblePublicationsQuery($userId, $authorId)->count()
            + self::eligibleForumsQuery($userId)->count()
            + self::eligibleForumCommentsQuery($userId)->count();
    }

    /**
     * @return array{
     *     resource_contributions: int,
     *     forum_posts: int,
     *     forum_comments: int,
     *     forum_contributions: int,
     *     total_contributions: int
     * }
     */
    public static function breakdownForUser(int $userId): array
    {
        $authorId = self::authorIdForUser($userId);

        $resourceContributions = self::eligiblePublicationsQuery($userId, $authorId)->count();
        $forumPosts = self::eligibleForumsQuery($userId)->count();
        $forumComments = self::eligibleForumCommentsQuery($userId)->count();
        $forumContributions = $forumPosts + $forumComments;

        return [
            'resource_contributions' => $resourceContributions,
            'forum_posts' => $forumPosts,
            'forum_comments' => $forumComments,
            'forum_contributions' => $forumContributions,
            'total_contributions' => $resourceContributions + $forumContributions,
        ];
    }
}
