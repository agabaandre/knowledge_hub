<?php

namespace App\Support;

use App\Models\CommunityOfPracticeMembers;
use Illuminate\Http\Request;

final class CommunityTargeting
{
    /**
     * @param  mixed  $communities
     * @return list<string>
     */
    public static function filterValidCommunityIds($communities): array
    {
        $arr = is_array($communities) ? $communities : [];
        $out = [];
        foreach ($arr as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            if (strtolower((string) $v) === 'all') {
                continue;
            }
            if (is_numeric($v) && (int) $v > 0) {
                $out[] = (string) (int) $v;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * @return list<string>
     */
    public static function approvedMembershipCommunityIds(int $userId): array
    {
        return CommunityOfPracticeMembers::query()
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->pluck('community_of_practice_id')
            ->map(fn ($id) => (string) (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function mergeTagAllIntoRequest(Request $request, string $key = 'communities'): void
    {
        if (! $request->boolean('tag_all_my_communities') || ! auth()->check()) {
            return;
        }
        $mine = self::approvedMembershipCommunityIds((int) auth()->id());
        $specific = self::filterValidCommunityIds($request->input($key, []));
        $request->merge([$key => array_values(array_unique(array_merge($specific, $mine)))]);
    }

    /**
     * True when content should appear on the main hub to everyone while still linked to CoPs.
     * Requires at least one community ID in the request (after mergeTagAllIntoRequest).
     */
    public static function wantsAlsoPublicOnHubWithCommunities(Request $request, string $communityKey = 'communities'): bool
    {
        if (! $request->boolean('also_public_with_communities')) {
            return false;
        }

        return count(self::filterValidCommunityIds($request->input($communityKey, []))) > 0;
    }
}
