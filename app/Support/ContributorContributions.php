<?php

namespace App\Support;

use App\Models\BadgeType;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    public static function publicationDedupKey(object $publication): string
    {
        $doi = trim((string) ($publication->doi ?? ''));
        if ($doi !== '') {
            return 'doi:'.Str::lower($doi);
        }

        $link = trim((string) ($publication->publication ?? ''));
        if ($link !== '') {
            return 'link:'.Str::lower($link);
        }

        $title = Str::lower(trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($publication->title ?? ''))) ?? ''));
        $owner = (int) ($publication->author_id ?: $publication->user_id ?: 0);

        return 'title:'.$title.'|owner:'.$owner;
    }

    /**
     * @param  callable(Builder): void|null  $scope
     */
    public static function distinctEligiblePublications(?int $userId = null, ?int $authorId = null, ?callable $scope = null): Collection
    {
        $query = self::eligiblePublicationsQuery($userId, $authorId)
            ->select(['id', 'title', 'doi', 'publication', 'author_id', 'user_id', 'created_at']);

        if ($scope) {
            $scope($query);
        }

        $seen = [];
        $unique = collect();

        foreach ($query->get() as $publication) {
            $key = self::publicationDedupKey($publication);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique->push($publication);
        }

        return $unique;
    }

    public static function countDistinctPublications(?int $userId = null, ?int $authorId = null, ?callable $scope = null): int
    {
        return self::distinctEligiblePublications($userId, $authorId, $scope)->count();
    }

    public static function countLifetimeForUser(int $userId): int
    {
        $authorId = self::authorIdForUser($userId);

        return self::countDistinctPublications($userId, $authorId)
            + self::eligibleForumsQuery($userId)->count()
            + self::eligibleForumCommentsQuery($userId)->count();
    }

    /**
     * @return array{
     *     raw_publications: int,
     *     resource_contributions: int,
     *     duplicate_publications_excluded: int,
     *     forum_posts: int,
     *     forum_comments: int,
     *     forum_contributions: int,
     *     total_contributions: int,
     *     earned_badge: ?BadgeType,
     *     earned_badge_name: ?string
     * }
     */
    public static function breakdownForUser(int $userId): array
    {
        $authorId = self::authorIdForUser($userId);
        $rawPublications = self::eligiblePublicationsQuery($userId, $authorId)->count();
        $resourceContributions = self::countDistinctPublications($userId, $authorId);
        $forumPosts = self::eligibleForumsQuery($userId)->count();
        $forumComments = self::eligibleForumCommentsQuery($userId)->count();
        $forumContributions = $forumPosts + $forumComments;
        $total = $resourceContributions + $forumContributions;
        $earnedBadge = BadgeType::getBadgeForContributions($total);

        return [
            'raw_publications' => $rawPublications,
            'resource_contributions' => $resourceContributions,
            'duplicate_publications_excluded' => max(0, $rawPublications - $resourceContributions),
            'forum_posts' => $forumPosts,
            'forum_comments' => $forumComments,
            'forum_contributions' => $forumContributions,
            'total_contributions' => $total,
            'earned_badge' => $earnedBadge,
            'earned_badge_name' => $earnedBadge?->name,
        ];
    }

    /**
     * @return array{
     *     user_id: int,
     *     stored_contributions: int,
     *     live_contributions: int,
     *     contribution_delta: int,
     *     stored_badge_id: ?int,
     *     stored_badge_name: ?string,
     *     earned_badge_id: ?int,
     *     earned_badge_name: ?string,
     *     status: string,
     *     breakdown: array<string, mixed>
     * }
     */
    public static function auditForUser(User $user): array
    {
        $user->loadMissing('lifetimeBadge.badgeType');
        $breakdown = self::breakdownForUser((int) $user->id);
        $stored = $user->lifetimeBadge;
        $storedContributions = (int) ($stored->lifetime_contributions ?? 0);
        $liveContributions = (int) $breakdown['total_contributions'];
        $storedBadgeId = $stored?->badge_type_id ? (int) $stored->badge_type_id : null;
        $earnedBadge = $breakdown['earned_badge'];
        $earnedBadgeId = $earnedBadge?->id ? (int) $earnedBadge->id : null;

        $status = 'ok';
        if ($storedBadgeId && ! $earnedBadgeId) {
            $status = 'over_awarded';
        } elseif ($storedContributions > $liveContributions || ($storedBadgeId && $earnedBadgeId && $storedBadgeId !== $earnedBadgeId && self::badgeTierRank($storedBadgeId) > self::badgeTierRank($earnedBadgeId))) {
            $status = 'stale';
        } elseif ($storedContributions < $liveContributions || ($earnedBadgeId && $storedBadgeId !== $earnedBadgeId)) {
            $status = 'under_awarded';
        }

        return [
            'user_id' => (int) $user->id,
            'stored_contributions' => $storedContributions,
            'live_contributions' => $liveContributions,
            'contribution_delta' => $storedContributions - $liveContributions,
            'stored_badge_id' => $storedBadgeId,
            'stored_badge_name' => $stored->badgeType->name ?? null,
            'earned_badge_id' => $earnedBadgeId,
            'earned_badge_name' => $earnedBadge?->name,
            'status' => $status,
            'breakdown' => $breakdown,
        ];
    }

    private static function badgeTierRank(int $badgeTypeId): int
    {
        return (int) (BadgeType::query()->whereKey($badgeTypeId)->value('contribution_threshold') ?? 0);
    }
}
