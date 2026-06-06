<?php

namespace App\Support;

use App\Models\Author;
use App\Models\User;

class ContributorStats
{
    /**
     * Contribution metrics aligned with the public author profile page.
     *
     * @return array{
     *     author: ?Author,
     *     public_profile_url: ?string,
     *     contribution_stats: array{
     *         resource_contributions: int,
     *         forum_posts: int,
     *         forum_comments: int,
     *         forum_contributions: int,
     *         total_contributions: int
     *     },
     *     badge_count: int
     * }
     */
    public static function forUser(User $user): array
    {
        $author = null;
        if (! empty($user->author_id)) {
            $author = Author::query()->find($user->author_id);
        }

        $breakdown = ContributorContributions::breakdownForUser((int) $user->id);
        $resourceContributions = $breakdown['resource_contributions'];
        $forumPosts = $breakdown['forum_posts'];
        $forumComments = $breakdown['forum_comments'];
        $forumContributions = $breakdown['forum_contributions'];

        $user->loadMissing('lifetimeBadge.badgeType');

        return [
            'author' => $author,
            'public_profile_url' => $author ? author_publications_url($author) : null,
            'lifetime_badge' => $user->lifetimeBadge,
            'contribution_stats' => [
                'resource_contributions' => $resourceContributions,
                'forum_posts' => $forumPosts,
                'forum_comments' => $forumComments,
                'forum_contributions' => $forumContributions,
                'total_contributions' => $breakdown['total_contributions'],
            ],
            'badge_count' => (int) optional($user->lifetimeBadge)->badge_type_id ? 1 : 0,
            'lifetime_badge' => $user->lifetimeBadge,
        ];
    }
}
