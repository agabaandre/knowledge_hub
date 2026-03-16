<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\Forum;
use App\Models\ForumCommunityOfPractice;
use App\Models\Publication;
use App\Models\Subscribe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyDigest extends Command
{
    protected $signature = 'mailing:weekly-digest';

    protected $description = 'Send weekly digest email to all subscribed users (new resources, forums, community activity)';

    public function handle()
    {
        $since = Carbon::now()->subDays(7);

        // New approved publications (created or date_created in last 7 days)
        $publications = Publication::where('is_approved', 1)
            ->where('is_rejected', 0)
            ->where(function ($q) use ($since) {
                $q->where('created_at', '>=', $since)
                    ->orWhere('date_created', '>=', $since);
            })
            ->with('author')
            ->orderByRaw('COALESCE(date_created, created_at) DESC')
            ->limit(20)
            ->get();

        // New approved forums (last 7 days)
        $forums = Forum::where('is_approved', 1)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            })
            ->where('created_at', '>=', $since)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Forums linked to communities (for "community activity" section)
        $forumIds = $forums->pluck('id')->toArray();
        $communityForumIds = [];
        if (!empty($forumIds)) {
            $communityForumIds = ForumCommunityOfPractice::whereIn('forum_id', $forumIds)
                ->pluck('forum_id')
                ->unique()
                ->values()
                ->toArray();
        }

        // New approved community members (last 7 days) – "new members joined"
        $newMembers = CommunityOfPracticeMembers::where('is_approved', 1)
            ->where('created_at', '>=', $since)
            ->with(['community', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // Build digest data for the view
        $digest = [
            'publications' => $publications,
            'forums' => $forums,
            'communityForumIds' => $communityForumIds,
            'newMembers' => $newMembers,
            'since' => $since,
            'sinceFormatted' => $since->format('F j, Y'),
            'weekEndFormatted' => Carbon::now()->format('F j, Y'),
        ];

        // Get all subscribed recipients (same logic as MailingListController)
        $recipients = collect([]);

        $subscribes = Subscribe::where('status', 'subscribed')->get()->map(function ($sub) {
            return (object) [
                'email' => $sub->email,
                'name' => $sub->name,
                'type' => 'subscribe',
            ];
        });
        $recipients = $recipients->merge($subscribes);

        $users = User::where('is_subscribed', 1)->get()->map(function ($user) {
            return (object) [
                'email' => $user->email,
                'name' => $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'type' => 'user',
            ];
        });
        $recipients = $recipients->merge($users);

        // Deduplicate by email
        $emailMap = [];
        $uniqueRecipients = collect([]);
        foreach ($recipients as $r) {
            $key = strtolower($r->email);
            if (!isset($emailMap[$key]) && !empty(trim($r->email ?? ''))) {
                $emailMap[$key] = true;
                $uniqueRecipients->push($r);
            }
        }

        if ($uniqueRecipients->isEmpty()) {
            $this->info('No subscribed recipients. Skipping weekly digest.');
            return Command::SUCCESS;
        }

        $subject = 'Weekly Digest: New resources, forums & community activity – ' . $digest['sinceFormatted'] . ' to ' . $digest['weekEndFormatted'];

        $sentCount = 0;
        $failedCount = 0;

        foreach ($uniqueRecipients as $subscriber) {
            try {
                $body = view('emails.weekly_digest', [
                    'subscriber' => $subscriber,
                    'digest' => $digest,
                    'subject' => $subject,
                ])->render();

                $mail = [
                    'email' => $subscriber->email,
                    'subject' => $subject,
                    'body' => $body,
                ];

                SendMailJob::dispatch($mail)->onQueue('default');
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Weekly digest failed for ' . $subscriber->email . ': ' . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Weekly digest queued: {$sentCount} emails, {$failedCount} failed.");
        return Command::SUCCESS;
    }
}
