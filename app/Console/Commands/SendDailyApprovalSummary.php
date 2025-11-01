<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\ForumComment;
use App\Models\PublicationComment;
use App\Models\CommunityOfPracticeMembers;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\Log;

class SendDailyApprovalSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approvals:daily-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily summary email of all pending approvals to approvers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all pending items
        $pendingForums = Forum::where('is_approved', 0)
            ->where('status', 0)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingPublications = Publication::where('is_approved', 0)
            ->where('is_rejected', 0)
            ->with(['user', 'author'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingForumComments = ForumComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingPublicationComments = PublicationComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingCopApprovals = CommunityOfPracticeMembers::where('is_approved', 0)
            ->with(['community', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Count totals
        $totalForums = Forum::where('is_approved', 0)->where('status', 0)->count();
        $totalPublications = Publication::where('is_approved', 0)->where('is_rejected', 0)->count();
        $totalForumComments = ForumComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->count();
        
        $totalPublicationComments = PublicationComment::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('status');
            })
            ->count();
        $totalCopApprovals = CommunityOfPracticeMembers::where('is_approved', 0)->count();

        $totalPending = $totalForums + $totalPublications + $totalForumComments + $totalPublicationComments + $totalCopApprovals;

        // If nothing pending, skip sending emails
        if ($totalPending === 0) {
            $this->info('No pending approvals. Skipping email notification.');
            return 0;
        }

        // Get all users with moderation permissions
        $forumModerators = User::permission('moderate_forum')->get();
        $publicationModerators = User::permission('moderate_publication')->get();
        
        // For COP approvals, we'll send to all admins or users with any moderation permission
        $copModerators = User::role(['admin', 'Administrator'])->get()
            ->merge($forumModerators)
            ->merge($publicationModerators)
            ->unique('id');

        // Combine all approvers (unique users)
        $allApprovers = $forumModerators->merge($publicationModerators)->merge($copModerators)->unique('id');

        if ($allApprovers->isEmpty()) {
            $this->warn('No approvers found with moderation permissions.');
            return 0;
        }

        $this->info("Sending daily approval summary to {$allApprovers->count()} approver(s)...");

        foreach ($allApprovers as $approver) {
            if (!$approver->email) {
                continue;
            }

            // Determine what this approver can moderate
            $canModerateForums = $approver->hasPermissionTo('moderate_forum');
            $canModeratePublications = $approver->hasPermissionTo('moderate_publication');
            $isAdmin = $approver->hasRole(['admin', 'Administrator']);

            // Build email content
            $body = view('emails.daily_approval_summary', [
                'approverName' => $approver->name,
                'pendingForums' => $canModerateForums ? $pendingForums : collect(),
                'pendingPublications' => $canModeratePublications ? $pendingPublications : collect(),
                'pendingForumComments' => $canModerateForums ? $pendingForumComments : collect(),
                'pendingPublicationComments' => $canModeratePublications ? $pendingPublicationComments : collect(),
                'pendingCopApprovals' => $isAdmin ? $pendingCopApprovals : collect(),
                'totalForums' => $canModerateForums ? $totalForums : 0,
                'totalPublications' => $canModeratePublications ? $totalPublications : 0,
                'totalForumComments' => $canModerateForums ? $totalForumComments : 0,
                'totalPublicationComments' => $canModeratePublications ? $totalPublicationComments : 0,
                'totalCopApprovals' => $isAdmin ? $totalCopApprovals : 0,
                'totalPending' => $totalPending,
            ])->render();

            $emailData = (object) [
                'email' => $approver->email,
                'subject' => 'Daily Approval Summary - ' . $totalPending . ' Item(s) Pending',
                'body' => $body,
                'title' => 'Daily Approval Summary'
            ];

            try {
                SendMailJob::dispatch($emailData)->onQueue('default');
                $this->info("  ✓ Queued email for: {$approver->email}");
            } catch (\Exception $e) {
                Log::error("Failed to queue daily approval summary email to {$approver->email}: " . $e->getMessage());
                $this->error("  ✗ Failed to queue email for: {$approver->email}");
            }
        }

        $this->info('Daily approval summary emails queued successfully!');
        return 0;
    }
}

