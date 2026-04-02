<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\CommunityOfPracticeMembers;
use App\Models\ContentRequest;
use App\Models\ContentRequestReferralMessage;
use App\Models\ForumComment;
use App\Models\User;

class ContentRequestReferralNotifier
{
    public static function notifyReferralCreated(ContentRequest $contentRequest): void
    {
        $contentRequest->load(['referredToUser', 'referredToCommunity', 'referredByUser', 'country']);
        $trackUrl = $contentRequest->trackUrl();
        $discussUrl = $contentRequest->discussionUrl();

        if ($contentRequest->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->email,
                'subject' => 'Your content request was referred for follow-up — '.$contentRequest->subject,
                'title' => 'Content request update',
                'body' => view('emails.content_request_referred_requestor', [
                    'contentRequest' => $contentRequest,
                    'trackUrl' => $trackUrl,
                ])->render(),
            ])->onQueue('default');
        }

        if ($contentRequest->referral_type === 'user' && $contentRequest->referredToUser?->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->referredToUser->email,
                'subject' => 'You have been asked to help with a content request',
                'title' => 'Content request assigned to you',
                'body' => view('emails.content_request_referred_assignee', [
                    'contentRequest' => $contentRequest,
                    'discussUrl' => $discussUrl,
                    'trackUrl' => $trackUrl,
                ])->render(),
            ])->onQueue('default');
        }

        // Community referrals: forum creation dispatches NotifyCommunityMembers (standard “new forum” email).
        // Avoid duplicating the same announcement with a second template unless there is no forum (legacy).
        if ($contentRequest->referral_type === 'community'
            && $contentRequest->referred_to_community_id
            && empty($contentRequest->referral_forum_id)) {
            $memberUserIds = CommunityOfPracticeMembers::query()
                ->where('community_of_practice_id', $contentRequest->referred_to_community_id)
                ->where('is_approved', 1)
                ->pluck('user_id')
                ->unique()
                ->filter();

            $users = User::query()
                ->whereIn('id', $memberUserIds)
                ->whereNotNull('email')
                ->orderBy('name')
                ->limit(60)
                ->get(['id', 'name', 'email']);

            foreach ($users as $user) {
                SendMailJob::dispatch([
                    'to' => $user->email,
                    'subject' => 'Community discussion: content request — '.$contentRequest->subject,
                    'title' => 'Content request referred to your community',
                    'body' => view('emails.content_request_referred_community_member', [
                        'contentRequest' => $contentRequest,
                        'discussUrl' => $discussUrl,
                        'recipientName' => $user->name,
                    ])->render(),
                ])->onQueue('default');
            }
        }
    }

    public static function notifyRequestorNewForumComment(ContentRequest $contentRequest, ForumComment $comment): void
    {
        if (! $contentRequest->email || ! $contentRequest->referral_forum_id) {
            return;
        }

        $requester = User::query()->where('email', $contentRequest->email)->first();
        if ($requester && (int) $requester->id === (int) $comment->created_by) {
            return;
        }

        SendMailJob::dispatch([
            'to' => $contentRequest->email,
            'subject' => 'New comment on your content request discussion — '.$contentRequest->subject,
            'title' => 'Update on your request',
            'body' => view('emails.content_request_forum_new_comment', [
                'contentRequest' => $contentRequest,
                'comment' => $comment,
                'forumUrl' => url('forums/thread?id='.$contentRequest->referral_forum_id),
            ])->render(),
        ])->onQueue('default');
    }

    public static function notifyRequestorNewMessage(ContentRequest $contentRequest, ContentRequestReferralMessage $message): void
    {
        if (! $contentRequest->email) {
            return;
        }

        SendMailJob::dispatch([
            'to' => $contentRequest->email,
            'subject' => 'New update on your content request — '.$contentRequest->subject,
            'title' => 'New message on your request',
            'body' => view('emails.content_request_referral_new_message', [
                'contentRequest' => $contentRequest,
                'message' => $message,
                'trackUrl' => $contentRequest->trackUrl(),
            ])->render(),
        ])->onQueue('default');
    }

    /**
     * When the requester posts via the public tracking link, notify the assigned hub user or community creator.
     */
    public static function notifyParticipantsRequestorReplied(ContentRequest $contentRequest, ContentRequestReferralMessage $message): void
    {
        $contentRequest->loadMissing(['referredToUser', 'referredToCommunity.creator']);

        if ($contentRequest->referral_type === 'user' && $contentRequest->referredToUser?->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->referredToUser->email,
                'subject' => 'The requester replied on a referred content request',
                'title' => 'New reply from requester',
                'body' => view('emails.content_request_requestor_replied', [
                    'contentRequest' => $contentRequest,
                    'message' => $message,
                    'discussUrl' => $contentRequest->discussionUrl(),
                ])->render(),
            ])->onQueue('default');

            return;
        }

        if ($contentRequest->referral_type === 'community' && $contentRequest->referredToCommunity?->creator?->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->referredToCommunity->creator->email,
                'subject' => 'The requester replied on a community content request',
                'title' => 'New reply from requester',
                'body' => view('emails.content_request_requestor_replied', [
                    'contentRequest' => $contentRequest,
                    'message' => $message,
                    'discussUrl' => $contentRequest->discussionUrl(),
                ])->render(),
            ])->onQueue('default');
        }
    }
}
