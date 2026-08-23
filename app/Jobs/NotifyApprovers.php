<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendMailJob;
use App\Support\ApprovalNotifications;

class NotifyApprovers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contentType; // 'publication' or 'forum'
    protected $contentId;
    protected $contentTitle;
    protected $contentDescription;
    protected $authorName;
    protected $approveUrl;
    protected $permissionType; // 'moderate_publication' or 'moderate_forum'

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contentType, $contentId, $contentTitle, $contentDescription = '', $authorName = '', $approveUrl = '')
    {
        $this->contentType = $contentType;
        $this->contentId = $contentId;
        $this->contentTitle = $contentTitle;
        $this->contentDescription = $contentDescription;
        $this->authorName = $authorName;
        $this->approveUrl = ApprovalNotifications::inboxUrl($contentType, $contentTitle);
        $permissions = ApprovalNotifications::permissionsFor($contentType);
        $this->permissionType = $permissions[0] ?? 'moderate_forum';
        
        // Set queue to 'default'
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $approvers = ApprovalNotifications::recipientsFor($this->contentType);

        if ($approvers->isEmpty()) {
            \Log::warning('No approvers found for pending '.$this->contentType.' approval.');
            return;
        }

        $typeLabel = ApprovalNotifications::typeLabel($this->contentType);

        foreach ($approvers as $approver) {
            if (!$approver->email) {
                continue;
            }

            $subject = 'New '.$typeLabel.' awaiting approval';

            $body = view('emails.approval_notification', [
                'contentType' => $typeLabel,
                'contentTitle' => $this->contentTitle,
                'contentDescription' => $this->contentDescription,
                'approveUrl' => $this->approveUrl,
                'authorName' => $this->authorName,
                'approverName' => $approver->name,
                'contentId' => $this->contentId,
            ])->render();

            // Send email using the existing email helper
            $emailData = (object) [
                'email' => $approver->email,
                'subject' => $subject,
                'body' => $body,
                'title' => $subject
            ];

            try {
                SendMailJob::dispatch($emailData)->onQueue('default');
            } catch (\Exception $e) {
                \Log::error('Failed to queue approval notification email to ' . $approver->email . ': ' . $e->getMessage());
            }
        }
    }
}
