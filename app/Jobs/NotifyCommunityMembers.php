<?php

namespace App\Jobs;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyCommunityMembers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var list<int> */
    protected array $communityIds;

    protected string $contentType;

    protected int $contentId;

    protected string $contentTitle;

    protected string $contentDescription;

    protected string $authorName;

    protected ?int $excludeUserId;

    /**
     * @param  array<int|string>  $communityIds
     */
    public function __construct(
        $communityIds,
        $contentType,
        $contentId,
        $contentTitle,
        $contentDescription = '',
        $authorName = '',
        ?int $excludeUserId = null
    ) {
        $this->communityIds = array_values(array_unique(array_filter(array_map('intval', (array) $communityIds))));
        $this->contentType = (string) $contentType;
        $this->contentId = (int) $contentId;
        $this->contentTitle = (string) $contentTitle;
        $this->contentDescription = (string) $contentDescription;
        $this->authorName = (string) $authorName;
        $this->excludeUserId = $excludeUserId;

        $this->onQueue('default');
    }

    public function handle(): void
    {
        if ($this->communityIds === []) {
            return;
        }

        $members = CommunityOfPracticeMembers::query()
            ->whereIn('community_of_practice_id', $this->communityIds)
            ->where('is_approved', 1)
            ->with(['user', 'community'])
            ->get()
            ->unique('user_id');

        $communityLabel = count($this->communityIds) === 1
            ? (optional(CommunityOfPractice::find($this->communityIds[0]))->community_name ?? 'Community')
            : 'your communities';

        foreach ($members as $member) {
            if ($this->excludeUserId !== null && (int) $member->user_id === $this->excludeUserId) {
                continue;
            }
            if (! $member->user || ! $member->user->email) {
                continue;
            }

            $contentUrl = '';
            if ($this->contentType === 'publication') {
                $contentUrl = publication_url($this->contentId);
            } elseif ($this->contentType === 'forum') {
                $contentUrl = forum_thread_url($this->contentId);
            }

            $subject = 'New '.ucfirst($this->contentType).' in Your Community';

            $body = view('emails.community_notification', [
                'contentType' => $this->contentType,
                'contentTitle' => $this->contentTitle,
                'contentDescription' => $this->contentDescription,
                'contentUrl' => $contentUrl,
                'authorName' => $this->authorName,
                'memberName' => $member->user->name,
                'communityName' => $communityLabel,
            ])->render();

            $emailData = (object) [
                'email' => $member->user->email,
                'subject' => $subject,
                'body' => $body,
                'title' => $subject,
            ];

            SendMailJob::dispatch($emailData)->onQueue('default');
        }
    }
}
