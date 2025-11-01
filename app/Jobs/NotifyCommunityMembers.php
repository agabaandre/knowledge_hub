<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CommunityOfPracticeMembers;
use App\Models\User;
use App\Jobs\SendMailJob;

class NotifyCommunityMembers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $communityIds;
    protected $contentType; // 'publication' or 'forum'
    protected $contentId;
    protected $contentTitle;
    protected $contentDescription;
    protected $authorName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($communityIds, $contentType, $contentId, $contentTitle, $contentDescription = '', $authorName = '')
    {
        $this->communityIds = is_array($communityIds) ? $communityIds : [$communityIds];
        $this->contentType = $contentType;
        $this->contentId = $contentId;
        $this->contentTitle = $contentTitle;
        $this->contentDescription = $contentDescription;
        $this->authorName = $authorName;
        
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
        foreach ($this->communityIds as $communityId) {
            // Get all approved members of the community
            $members = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
                ->where('is_approved', 1)
                ->with(['user', 'community'])
                ->get();

            foreach ($members as $member) {
                if ($member->user && $member->user->email) {
                    // Build content URL based on type
                    $contentUrl = '';
                    if ($this->contentType === 'publication') {
                        $contentUrl = url('records/resource') . '?id=' . $this->contentId;
                    } elseif ($this->contentType === 'forum') {
                        $contentUrl = url('forums/thread') . '?id=' . $this->contentId;
                    }

                    // Build email subject and body
                    $subject = 'New ' . ucfirst($this->contentType) . ' in Your Community';
                    
                    $body = view('emails.community_notification', [
                        'contentType' => $this->contentType,
                        'contentTitle' => $this->contentTitle,
                        'contentDescription' => $this->contentDescription,
                        'contentUrl' => $contentUrl,
                        'authorName' => $this->authorName,
                        'memberName' => $member->user->name,
                        'communityName' => $member->community->community_name ?? 'Community'
                    ])->render();

                    // Send email using the existing email helper
                    $emailData = (object) [
                        'email' => $member->user->email,
                        'subject' => $subject,
                        'body' => $body,
                        'title' => $subject
                    ];

                    SendMailJob::dispatch($emailData)->onQueue('default');
                }
            }
        }
    }
}

