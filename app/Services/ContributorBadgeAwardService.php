<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\BadgeType;
use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumCommunityOfPractice;
use App\Models\PublicationCommunityOfPractice;
use App\Models\User;
use App\Models\UserCommunityMonthlyContribution;
use App\Models\UserLifetimeBadge;
use App\Support\ContributorContributions;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
            $userIds = $this->userIdsWithActivityInMonth($year, $month)
                ->merge($this->userIdsWithExistingBadges())
                ->filter()
                ->unique()
                ->values();

            if ($userIds->isEmpty()) {
                $result['status'] = 'skipped';
                $result['error'] = 'No contributor activity or existing badge holders found for this period.';

                return $result;
            }

            $totalUsers = $userIds->count();
            $this->updateJobProgress(0, $totalUsers, $year, $month, $triggeredBy);

            foreach ($userIds as $userId) {
                $user = User::query()->find($userId);
                if (! $user) {
                    continue;
                }

                $result['users_processed']++;
                $this->updateJobProgress($result['users_processed'], $totalUsers, $year, $month, $triggeredBy);

                $upgrade = $this->recalculateLifetimeBadge($user);
                if ($upgrade['created']) {
                    $result['badges_awarded']++;
                }
                if ($upgrade['upgraded']) {
                    $result['badges_upgraded']++;
                }

                if ($user->email && $upgrade['badge_type']) {
                    if ($upgrade['created']) {
                        $this->queueLifetimeAwardEmail($user, $upgrade['badge_type'], (int) $upgrade['lifetime_contributions']);
                        $result['emails_queued']++;
                    } elseif ($upgrade['upgraded']) {
                        $this->queueLifetimeUpgradeEmail($user, $upgrade['badge_type'], (int) $upgrade['lifetime_contributions']);
                        $result['emails_queued']++;
                    }
                }

                $result['community_rows_synced'] += $this->syncCommunityMonthlyContributions($user, $year, $month);
            }
        } catch (\Throwable $e) {
            Cache::forget('badges_award_job_progress');

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
        Cache::forget('badges_award_job_progress');

        return $result;
    }

    /**
     * @return array{
     *     total: int,
     *     mismatches: int,
     *     rows: list<array<string, mixed>>
     * }
     */
    public function auditBadgeHolders(bool $onlyMismatches = true, int $limit = 200): array
    {
        $rows = [];
        $mismatches = 0;

        $query = UserLifetimeBadge::query()
            ->with(['user.author', 'badgeType'])
            ->whereNotNull('badge_type_id')
            ->orderByDesc('lifetime_contributions');

        $limitReached = false;
        $query->chunk(100, function ($badges) use (&$rows, &$mismatches, $onlyMismatches, $limit, &$limitReached) {
            if ($limitReached) {
                return false;
            }

            foreach ($badges as $badge) {
                if (count($rows) >= $limit) {
                    $limitReached = true;

                    return false;
                }

                if (! $badge->user) {
                    continue;
                }

                $audit = ContributorContributions::auditForUser($badge->user);
                if ($onlyMismatches && $audit['status'] === 'ok') {
                    continue;
                }

                if ($audit['status'] !== 'ok') {
                    $mismatches++;
                }

                $rows[] = array_merge($audit, [
                    'name' => $badge->user->name,
                    'email' => $badge->user->email,
                    'author_name' => $badge->user->author->name ?? null,
                    'last_upgraded_at' => optional($badge->last_upgraded_at)->toDateTimeString(),
                ]);
            }
        });

        return [
            'total' => UserLifetimeBadge::query()->whereNotNull('badge_type_id')->count(),
            'mismatches' => $mismatches,
            'rows' => $rows,
        ];
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
        return ContributorContributions::countLifetimeForUser($userId);
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
            $keys = [
                'user_id' => $user->id,
                'community_of_practice_id' => $community->id,
                'year' => $year,
                'month' => $month,
            ];
            $count = $this->countCommunityContributionsForMonth((int) $user->id, (int) $community->id, $year, $month);

            if ($count <= 0) {
                UserCommunityMonthlyContribution::query()->where($keys)->delete();

                continue;
            }

            UserCommunityMonthlyContribution::query()->updateOrCreate($keys, ['contributions_count' => $count]);
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
        $authorId = ContributorContributions::authorIdForUser($userId);

        $publicationIds = PublicationCommunityOfPractice::query()
            ->where('community_of_practice_id', $communityId)
            ->pluck('publication_id');

        $publications = ContributorContributions::countDistinctPublications(
            $userId,
            $authorId,
            function ($query) use ($publicationIds, $year, $month) {
                $query->whereIn('id', $publicationIds)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
            }
        );

        $forumIds = ForumCommunityOfPractice::query()
            ->where('community_of_practice_id', $communityId)
            ->pluck('forum_id');

        $forumPosts = ContributorContributions::eligibleForumsQuery($userId)
            ->whereIn('id', $forumIds)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $forumComments = ContributorContributions::eligibleForumCommentsQuery($userId)
            ->whereIn('forum_id', $forumIds)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        return $publications + $forumPosts + $forumComments;
    }

    /**
     * Contributors recognised this calendar month (new badge or tier upgrade).
     *
     * @return Collection<int, UserLifetimeBadge>
     */
    public function badgeRecognitionsForMonth(?int $year = null, ?int $month = null): Collection
    {
        $year = $year ?? (int) now()->year;
        $month = $month ?? (int) now()->month;

        return UserLifetimeBadge::query()
            ->with(['user:id,name,first_name,last_name,email,author_id', 'badgeType:id,name,slug'])
            ->whereNotNull('badge_type_id')
            ->whereNotNull('last_upgraded_at')
            ->whereYear('last_upgraded_at', $year)
            ->whereMonth('last_upgraded_at', $month)
            ->orderByDesc('last_upgraded_at')
            ->limit(25)
            ->get();
    }

    private function userIdsWithExistingBadges(): Collection
    {
        return UserLifetimeBadge::query()->pluck('user_id');
    }

    private function userIdsWithActivityInMonth(int $year, int $month): Collection
    {
        $ids = collect();

        $ids = $ids->merge(
            ContributorContributions::eligiblePublicationsQuery()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereNotNull('user_id')
                ->pluck('user_id')
        );

        $authorIds = ContributorContributions::eligiblePublicationsQuery()
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
                ->where('status', 1)
                ->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->pluck('created_by')
        );

        $ids = $ids->merge(
            ForumComment::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereHas('forum', function ($q) {
                    $q->where('status', 1)
                        ->where('is_approved', 1)
                        ->where(function ($q) {
                            $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                        });
                })
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

    private function queueLifetimeAwardEmail(User $user, BadgeType $badgeType, int $lifetimeContributions): void
    {
        $subject = 'You earned a contributor badge — '.$badgeType->name;

        $body = view('emails.badge_lifetime_awarded', [
            'userName' => $user->name,
            'badgeType' => $badgeType,
            'lifetimeContributions' => $lifetimeContributions,
            'profileUrl' => $user->author_id ? author_publications_url((int) $user->author_id) : url('/account'),
        ])->render();

        $this->dispatchBadgeEmail($user, $subject, $body);
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

        $this->dispatchBadgeEmail($user, $subject, $body);
    }

    private function dispatchBadgeEmail(User $user, string $subject, string $body): void
    {
        SendMailJob::dispatch((object) [
            'email' => $user->email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject,
        ])->onQueue('default');
    }

    private function updateJobProgress(int $processed, int $total, int $year, int $month, string $triggeredBy): void
    {
        Cache::put('badges_award_job_progress', [
            'total' => $total,
            'processed' => $processed,
            'percent' => $total > 0 ? (int) round(($processed / $total) * 100) : 0,
            'year' => $year,
            'month' => $month,
            'triggered_by' => $triggeredBy,
            'updated_at' => now()->toIso8601String(),
        ], now()->addHours(2));
    }
}
