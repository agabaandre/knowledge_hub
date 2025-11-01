<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Jobs\SendMailJob;

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
        $this->approveUrl = $approveUrl;
        
        // Determine permission based on content type
        $this->permissionType = $contentType === 'publication' ? 'moderate_publication' : 'moderate_forum';
        
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
        // Get all users with the permission to approve this content type
        $approvers = User::permission($this->permissionType)->get();
        
        if ($approvers->isEmpty()) {
            \Log::warning("No approvers found with permission: {$this->permissionType}");
            return;
        }

        foreach ($approvers as $approver) {
            if (!$approver->email) {
                continue;
            }

            // Build email subject and body
            $subject = 'New ' . ucfirst($this->contentType) . ' Awaiting Approval';
            
            $body = view('emails.approval_notification', [
                'contentType' => $this->contentType,
                'contentTitle' => $this->contentTitle,
                'contentDescription' => $this->contentDescription,
                'approveUrl' => $this->approveUrl,
                'authorName' => $this->authorName,
                'approverName' => $approver->name,
                'contentId' => $this->contentId
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
