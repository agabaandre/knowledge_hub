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
use App\Models\FederatedContentItem;
use App\Support\ApprovalNotifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        $pendingFederated = collect();
        $totalFederated = 0;
        if (Schema::hasTable('federated_content_items')) {
            $pendingFederated = FederatedContentItem::query()->pendingReview()->with('hub')->orderByDesc('id')->limit(10)->get();
            $totalFederated = FederatedContentItem::query()->pendingReview()->count();
        }

        $totalPending = $totalForums + $totalPublications + $totalForumComments + $totalPublicationComments + $totalCopApprovals + $totalFederated;

        // If nothing pending, skip sending emails
        if ($totalPending === 0) {
            $this->info('No pending approvals. Skipping email notification.');
            return 0;
        }

        $allApprovers = collect()
            ->merge(ApprovalNotifications::recipientsFor('publication'))
            ->merge(ApprovalNotifications::recipientsFor('forum'))
            ->merge(ApprovalNotifications::recipientsFor('cop_participant'))
            ->merge(ApprovalNotifications::recipientsFor('federated'))
            ->unique('id');

        if ($allApprovers->isEmpty()) {
            $this->warn('No approvers found with moderation permissions.');
            return 0;
        }

        $this->info("Sending daily approval summary to {$allApprovers->count()} approver(s)...");

        foreach ($allApprovers as $approver) {
            if (!$approver->email) {
                continue;
            }

            $canModerateForums = ApprovalNotifications::canApprove($approver, 'forum');
            $canModeratePublications = ApprovalNotifications::canApprove($approver, 'publication');
            $canModerateCop = ApprovalNotifications::canApprove($approver, 'cop_participant');
            $canModerateFederated = ApprovalNotifications::canApprove($approver, 'federated');

            $userPending = ($canModerateForums ? $totalForums + $totalForumComments : 0)
                + ($canModeratePublications ? $totalPublications + $totalPublicationComments : 0)
                + ($canModerateCop ? $totalCopApprovals : 0)
                + ($canModerateFederated ? $totalFederated : 0);
            if ($userPending === 0) {
                continue;
            }

            $body = view('emails.daily_approval_summary', [
                'approverName' => $approver->name,
                'pendingForums' => $canModerateForums ? $pendingForums : collect(),
                'pendingPublications' => $canModeratePublications ? $pendingPublications : collect(),
                'pendingForumComments' => $canModerateForums ? $pendingForumComments : collect(),
                'pendingPublicationComments' => $canModeratePublications ? $pendingPublicationComments : collect(),
                'pendingCopApprovals' => $canModerateCop ? $pendingCopApprovals : collect(),
                'pendingFederated' => $canModerateFederated ? $pendingFederated : collect(),
                'totalForums' => $canModerateForums ? $totalForums : 0,
                'totalPublications' => $canModeratePublications ? $totalPublications : 0,
                'totalForumComments' => $canModerateForums ? $totalForumComments : 0,
                'totalPublicationComments' => $canModeratePublications ? $totalPublicationComments : 0,
                'totalCopApprovals' => $canModerateCop ? $totalCopApprovals : 0,
                'totalFederated' => $canModerateFederated ? $totalFederated : 0,
                'totalPending' => $userPending,
            ])->render();

            $emailData = (object) [
                'email' => $approver->email,
                'subject' => 'Daily Approval Summary - ' . $userPending . ' Item(s) Pending',
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

