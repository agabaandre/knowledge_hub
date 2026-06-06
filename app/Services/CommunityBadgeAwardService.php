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
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CommunityBadgeAwardService
{
    /**
     * @return array{
     *     status: string,
     *     year: int,
     *     month: int,
     *     period_label: string,
     *     badges_awarded: int,
     *     emails_queued: int,
     *     communities_processed: int,
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
            'emails_queued' => 0,
            'communities_processed' => 0,
            'triggered_by' => $triggeredBy,
            'finished_at' => now()->toIso8601String(),
            'error' => null,
        ];

        try {
            $communities = CommunityOfPractice::query()->where('is_active', true)->get();

            if ($communities->isEmpty()) {
                $result['status'] = 'skipped';
                $result['error'] = 'No active communities found.';

                return $result;
            }

            foreach ($communities as $community) {
                $result['communities_processed']++;

                $members = $community->membership()
                    ->where('is_approved', 1)
                    ->with('user')
                    ->get();

                foreach ($members as $member) {
                    if (! $member->user) {
                        continue;
                    }

                    $userId = (int) $member->user_id;
                    $contributions = $this->countContributions($userId, (int) $community->id, $year, $month);

                    if ($contributions === 0) {
                        continue;
                    }

                    $badgeType = BadgeType::getBadgeForContributions($contributions);
                    if (! $badgeType) {
                        continue;
                    }

                    if (UserBadge::hasBadge($userId, $community->id, $badgeType->id, $year, $month)) {
                        continue;
                    }

                    $userBadge = UserBadge::create([
                        'user_id' => $userId,
                        'community_of_practice_id' => $community->id,
                        'badge_type_id' => $badgeType->id,
                        'year' => $year,
                        'month' => $month,
                        'contributions_count' => $contributions,
                        'awarded_at' => now(),
                        'email_sent' => false,
                    ]);

                    $result['badges_awarded']++;

                    if ($member->user->email) {
                        $this->queueBadgeNotification($member->user, $community, $badgeType, $contributions, $year, $month);
                        $userBadge->email_sent = true;
                        $userBadge->save();
                        $result['emails_queued']++;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('CommunityBadgeAwardService failed', [
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

    private function countContributions(int $userId, int $communityId, int $year, int $month): int
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

    private function queueBadgeNotification($user, $community, $badgeType, int $contributions, int $year, int $month): void
    {
        $subject = 'Congratulations! You Earned a '.$badgeType->name.' Badge';

        $body = view('emails.badge_awarded', [
            'userName' => $user->name,
            'communityName' => $community->community_name,
            'badgeType' => $badgeType,
            'contributions' => $contributions,
            'monthYear' => Carbon::create($year, $month, 1)->format('F Y'),
            'communityUrl' => url('communities').'?term='.urlencode($community->community_name),
        ])->render();

        $emailData = (object) [
            'email' => $user->email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject,
        ];

        SendMailJob::dispatch($emailData)->onQueue('default');
    }
}
