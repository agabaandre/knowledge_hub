<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\BadgeType;
use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumCommunityOfPractice;
use App\Models\Publication;
use App\Models\PublicationCommunityOfPractice;
use App\Models\User;
use App\Models\UserCommunityMonthlyContribution;
use App\Models\UserLifetimeBadge;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ContributorBadgeAwardService
{
    /**
     * @return array{
     *     status: string,
     *     year: int,
     *     month: int,
     *     period_label: string,
     *     badges_awarded: int,
     *     badges_upgraded: int,
     *     emails_queued: int,
     *     users_processed: int,
     *     community_rows_synced: int,
     *     triggered_by: string,
     *     finished_at: string,
     *     error: ?string
     * }
     */
    public function awardForPeriod(int $year, int $month, string $triggeredBy = 'cli'): array
    {
        $result = [
            'status' => 'completed',
            'year' => $year,
            'month' => $month,
            'period_label' => Carbon::create($year, $month, 1)->format('F Y'),
            'badges_awarded' => 0,
            'badges_upgraded' => 0,
            'emails_queued' => 0,
            'users_processed' => 0,
            'community_rows_synced' => 0,
            'triggered_by' => $triggeredBy,
            'finished_at' => now()->toIso8601String(),
            'error' => null,
        ];

        try {
            $userIds = $this->userIdsWithActivityInMonth($year, $month);

            if ($userIds->isEmpty()) {
                $result['status'] = 'skipped';
                $result['error'] = 'No contributor activity found for this period.';

                return $result;
            }

            foreach ($userIds as $userId) {
                $user = User::query()->find($userId);
                if (! $user) {
                    continue;
                }

                $result['users_processed']++;

                $upgrade = $this->recalculateLifetimeBadge($user);
                if ($upgrade['created']) {
                    $result['badges_awarded']++;
                }
                if ($upgrade['upgraded']) {
                    $result['badges_upgraded']++;
                    if ($user->email && $upgrade['badge_type']) {
                        $this->queueLifetimeUpgradeEmail($user, $upgrade['badge_type'], (int) $upgrade['lifetime_contributions']);
                        $result['emails_queued']++;
                    }
                }

                $result['community_rows_synced'] += $this->syncCommunityMonthlyContributions($user, $year, $month);
            }
        } catch (\Throwable $e) {
            Log::error('ContributorBadgeAwardService failed', [
                'year' => $year,
                'month' => $month,
                'triggered_by' => $triggeredBy,
                'message' => $e->getMessage(),
            ]);

            $result['status'] = 'failed';
            $result['error'] = $e->getMessage();
        }

        $result['finished_at'] = now()->toIso8601String();

        return $result;
    }

    public function defaultPeriod(): array
    {
        $date = Carbon::now()->subMonth();

        return [
            'year' => (int) $date->year,
            'month' => (int) $date->month,
        ];
    }

    public function countLifetimeContributions(int $userId): int
    {
        $authorId = User::query()->whereKey($userId)->value('author_id');

        $publications = Publication::query()
            ->where('is_version', 0)
            ->where('is_approved', 1)
            ->where(function ($q) use ($userId, $authorId) {
                $q->where('user_id', $userId);
                if ($authorId) {
                    $q->orWhere('author_id', $authorId);
                }
            })
            ->count();

        $forumPosts = Forum::query()
            ->where('created_by', $userId)
            ->where('status', 1)
            ->where('is_approved', 1)
            ->count();

        $forumComments = ForumComment::query()
            ->where('created_by', $userId)
            ->whereHas('forum', function ($q) {
                $q->where('status', 1);
            })
            ->count();

        return $publications + $forumPosts + $forumComments;
    }

    /**
     * @return array{created: bool, upgraded: bool, badge_type: ?BadgeType, lifetime_contributions: int}
     */
    public function recalculateLifetimeBadge(User $user): array
    {
        $lifetime = $this->countLifetimeContributions((int) $user->id);
        $badgeType = BadgeType::getBadgeForContributions($lifetime);

        $record = UserLifetimeBadge::query()->firstOrNew(['user_id' => $user->id]);
        $previousTypeId = $record->badge_type_id;
        $created = ! $record->exists;

        $record->lifetime_contributions = $lifetime;
        $record->badge_type_id = $badgeType?->id;

        $upgraded = false;
        if ($badgeType && $badgeType->id !== $previousTypeId) {
            $previousType = $previousTypeId ? BadgeType::query()->find($previousTypeId) : null;
            $upgraded = $this->isStrongerBadge($badgeType, $previousType);
            if ($upgraded || $created) {
                $record->last_upgraded_at = now();
            }
        }

        $record->save();

        return [
            'created' => $created && $badgeType !== null,
            'upgraded' => $upgraded,
            'badge_type' => $badgeType,
            'lifetime_contributions' => $lifetime,
        ];
    }

    public function syncCommunityMonthlyContributions(User $user, int $year, int $month): int
    {
        $synced = 0;
        $communities = CommunityOfPractice::query()->where('is_active', true)->get(['id']);

        foreach ($communities as $community) {
            $count = $this->countCommunityContributionsForMonth((int) $user->id, (int) $community->id, $year, $month);
            if ($count <= 0) {
                continue;
            }

            UserCommunityMonthlyContribution::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'community_of_practice_id' => $community->id,
                    'year' => $year,
                    'month' => $month,
                ],
                ['contributions_count' => $count]
            );
            $synced++;
        }

        return $synced;
    }

    /**
     * @return Collection<int, UserCommunityMonthlyContribution>
     */
    public function communityContributionsForMonth(int $userId, int $year, int $month): Collection
    {
        return UserCommunityMonthlyContribution::query()
            ->with('community:id,community_name,slug')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('contributions_count', '>', 0)
            ->orderByDesc('contributions_count')
            ->get();
    }

    public function countCommunityContributionsForMonth(int $userId, int $communityId, int $year, int $month): int
    {
        $count = 0;
        $authorId = User::query()->whereKey($userId)->value('author_id');

        $publicationIds = PublicationCommunityOfPractice::query()
            ->where('community_of_practice_id', $communityId)
            ->pluck('publication_id');

        $count += Publication::query()
            ->whereIn('id', $publicationIds)
            ->where(function ($q) use ($userId, $authorId) {
                $q->where('user_id', $userId);
                if ($authorId) {
                    $q->orWhere('author_id', $authorId);
                }
            })
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('is_approved', 1)
            ->count();

        $forumIds = ForumCommunityOfPractice::query()
            ->where('community_of_practice_id', $communityId)
            ->pluck('forum_id');

        try {
            $count += Forum::query()
                ->whereIn('id', $forumIds)
                ->where('created_by', $userId)
                ->where('is_approved', 1)
                ->where('status', 1)
                ->whereRaw('YEAR(created_at) = ? AND MONTH(created_at) = ?', [$year, $month])
                ->count();
        } catch (\Throwable $e) {
            $count += Forum::query()
                ->whereIn('id', $forumIds)
                ->where('created_by', $userId)
                ->where('is_approved', 1)
                ->where('status', 1)
                ->count();
        }

        $count += ForumComment::query()
            ->whereIn('forum_id', $forumIds)
            ->where('created_by', $userId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        return $count;
    }

    private function userIdsWithActivityInMonth(int $year, int $month): Collection
    {
        $ids = collect();

        $ids = $ids->merge(
            Publication::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereNotNull('user_id')
                ->pluck('user_id')
        );

        $authorIds = Publication::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('author_id')
            ->pluck('author_id');

        if ($authorIds->isNotEmpty()) {
            $ids = $ids->merge(
                User::query()->whereIn('author_id', $authorIds)->pluck('id')
            );
        }

        $ids = $ids->merge(
            Forum::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->pluck('created_by')
        );

        $ids = $ids->merge(
            ForumComment::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->pluck('created_by')
        );

        return $ids->filter()->unique()->values();
    }

    private function isStrongerBadge(BadgeType $next, ?BadgeType $previous): bool
    {
        if ($previous === null) {
            return true;
        }

        return (int) $next->contribution_threshold > (int) $previous->contribution_threshold;
    }

    private function queueLifetimeUpgradeEmail(User $user, BadgeType $badgeType, int $lifetimeContributions): void
    {
        $subject = 'Your contributor badge has grown stronger — '.$badgeType->name;

        $body = view('emails.badge_lifetime_upgraded', [
            'userName' => $user->name,
            'badgeType' => $badgeType,
            'lifetimeContributions' => $lifetimeContributions,
            'profileUrl' => $user->author_id ? author_publications_url((int) $user->author_id) : url('/account'),
        ])->render();

        SendMailJob::dispatch((object) [
            'email' => $user->email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject,
        ])->onQueue('default');
    }
}
