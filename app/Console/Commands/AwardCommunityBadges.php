<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommunityOfPractice;
use App\Models\BadgeType;
use App\Models\UserBadge;
use App\Models\Publication;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\PublicationCommunityOfPractice;
use App\Models\ForumCommunityOfPractice;
use App\Jobs\SendMailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwardCommunityBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badges:award-community {--month=} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award badges to community members based on their monthly contributions (publications and forum engagements)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Determine the month/year to process (defaults to previous month)
        $year = $this->option('year') ?: Carbon::now()->subMonth()->year;
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;

        $this->info("Processing badges for {$year}-{$month}");

        // Get all active communities
        $communities = CommunityOfPractice::where('is_active', true)->get();
        
        if ($communities->isEmpty()) {
            $this->warn('No active communities found.');
            return 0;
        }

        $totalBadgesAwarded = 0;
        $totalEmailsSent = 0;

        foreach ($communities as $community) {
            $this->info("Processing community: {$community->community_name}");

            // Get all approved members of this community
            $members = $community->membership()
                ->where('is_approved', 1)
                ->with('user')
                ->get();

            foreach ($members as $member) {
                $userId = $member->user_id;
                
                if (!$member->user) {
                    continue;
                }

                // Count contributions for this user in this community for the specified month/year
                $contributions = $this->countContributions($userId, $community->id, $year, $month);

                if ($contributions == 0) {
                    continue;
                }

                // Get the highest badge this user qualifies for
                $badgeType = BadgeType::getBadgeForContributions($contributions);

                if (!$badgeType) {
                    continue;
                }

                // Check if user already has this badge for this month/year
                if (UserBadge::hasBadge($userId, $community->id, $badgeType->id, $year, $month)) {
                    $this->line("  User {$member->user->name} already has {$badgeType->name} badge for {$year}-{$month}");
                    continue;
                }

                // Award the badge
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

                $totalBadgesAwarded++;

                $this->info("  ✓ Awarded {$badgeType->name} badge to {$member->user->name} ({$contributions} contributions)");

                // Send email notification
                if ($member->user->email) {
                    $this->sendBadgeNotification($member->user, $community, $badgeType, $contributions, $year, $month);
                    $userBadge->email_sent = true;
                    $userBadge->save();
                    $totalEmailsSent++;
                }
            }
        }

        $this->info("\n✓ Badge awarding completed!");
        $this->info("  Total badges awarded: {$totalBadgesAwarded}");
        $this->info("  Total emails sent: {$totalEmailsSent}");

        return 0;
    }

    /**
     * Count contributions (publications + forum posts + forum comments) for a user in a community for a specific month/year
     */
    private function countContributions($userId, $communityId, $year, $month)
    {
        $count = 0;

        $authorId = User::query()->whereKey($userId)->value('author_id');

        // Count publications attached to this community created by this user in the specified month/year
        $publicationIds = PublicationCommunityOfPractice::where('community_of_practice_id', $communityId)
            ->pluck('publication_id');

        $publications = Publication::whereIn('id', $publicationIds)
            ->where(function ($q) use ($userId, $authorId) {
                $q->where('user_id', $userId);
                if ($authorId) {
                    $q->orWhere('author_id', $authorId);
                }
            })
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('is_approved', 1) // Only count approved publications
            ->count();

        $count += $publications;

        // Count forum posts in this community created by this user in the specified month/year
        $forumIds = ForumCommunityOfPractice::where('community_of_practice_id', $communityId)
            ->pluck('forum_id');

        // Note: Forum model has $timestamps = false, but the table may still have created_at column
        // Check if created_at column exists, otherwise use a different approach
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        try {
            // Try to query with created_at filter
            $forums = Forum::whereIn('id', $forumIds)
                ->where('created_by', $userId)
                ->where('is_approved', 1) // Only count approved forums
                ->where('status', 1) // Only count active forums
                ->whereRaw('YEAR(created_at) = ? AND MONTH(created_at) = ?', [$year, $month])
                ->count();
        } catch (\Exception $e) {
            // If created_at column doesn't exist, skip date filtering (count all approved forums)
            // This is a fallback - ideally forums should have created_at
            $forums = Forum::whereIn('id', $forumIds)
                ->where('created_by', $userId)
                ->where('is_approved', 1)
                ->where('status', 1)
                ->count();
        }

        $count += $forums;

        // Count forum comments in forums of this community created by this user in the specified month/year
        // ForumComment now has timestamps enabled
        $comments = ForumComment::whereIn('forum_id', $forumIds)
            ->where('created_by', $userId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $count += $comments;

        return $count;
    }

    /**
     * Send badge notification email
     */
    private function sendBadgeNotification($user, $community, $badgeType, $contributions, $year, $month)
    {
        $subject = 'Congratulations! You Earned a ' . $badgeType->name . ' Badge';

        $body = view('emails.badge_awarded', [
            'userName' => $user->name,
            'communityName' => $community->community_name,
            'badgeType' => $badgeType,
            'contributions' => $contributions,
            'monthYear' => Carbon::create($year, $month, 1)->format('F Y'),
            'communityUrl' => url('communities') . '?term=' . urlencode($community->community_name),
        ])->render();

        $emailData = (object) [
            'email' => $user->email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject
        ];

        try {
            SendMailJob::dispatch($emailData)->onQueue('default');
        } catch (\Exception $e) {
            \Log::error('Failed to queue badge notification email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'badge_type_id' => $badgeType->id,
            ]);
        }
    }
}
