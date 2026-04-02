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
        $contentRequest->load([
            'referredToUser',
            'referredToCommunity',
            'referredByUser',
            'country',
            'referralTargets.user',
            'referralTargets.community',
        ]);
        $trackUrl = $contentRequest->trackUrl();
        $hubDiscussUrl = route('content-request.referral.discuss', $contentRequest);

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

        $targets = $contentRequest->referralTargets;
        if ($targets->isEmpty()) {
            self::notifyReferralCreatedLegacy($contentRequest);

            return;
        }

        $emailedUserIds = [];

        foreach ($targets as $target) {
            if ($target->user_id && $target->user?->email) {
                $uid = (int) $target->user_id;
                if (isset($emailedUserIds[$uid])) {
                    continue;
                }
                $emailedUserIds[$uid] = true;
                SendMailJob::dispatch([
                    'to' => $target->user->email,
                    'subject' => 'You have been asked to help with a content request',
                    'title' => 'Content request assigned to you',
                    'body' => view('emails.content_request_referred_assignee', [
                        'contentRequest' => $contentRequest,
                        'discussUrl' => $hubDiscussUrl,
                        'trackUrl' => $trackUrl,
                    ])->render(),
                ])->onQueue('default');
            }

            // Community: forum path uses NotifyCommunityMembers from forum service; legacy email if no forum on target
            if ($target->community_of_practice_id && empty($target->referral_forum_id)) {
                $memberUserIds = CommunityOfPracticeMembers::query()
                    ->where('community_of_practice_id', $target->community_of_practice_id)
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
                            'discussUrl' => $contentRequest->discussionUrlForCommunity((int) $target->community_of_practice_id),
                            'recipientName' => $user->name,
                        ])->render(),
                    ])->onQueue('default');
                }
            }
        }
    }

    /**
     * @deprecated Used when referral_targets rows are missing (pre-migration data).
     */
    private static function notifyReferralCreatedLegacy(ContentRequest $contentRequest): void
    {
        $hubDiscussUrl = route('content-request.referral.discuss', $contentRequest);

        if ($contentRequest->referral_type === 'user' && $contentRequest->referredToUser?->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->referredToUser->email,
                'subject' => 'You have been asked to help with a content request',
                'title' => 'Content request assigned to you',
                'body' => view('emails.content_request_referred_assignee', [
                    'contentRequest' => $contentRequest,
                    'discussUrl' => $hubDiscussUrl,
                    'trackUrl' => $contentRequest->trackUrl(),
                ])->render(),
            ])->onQueue('default');
        }

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
                        'discussUrl' => $contentRequest->discussionUrlForCommunity((int) $contentRequest->referred_to_community_id),
                        'recipientName' => $user->name,
                    ])->render(),
                ])->onQueue('default');
            }
        }
    }

    public static function notifyRequestorNewForumComment(ContentRequest $contentRequest, ForumComment $comment): void
    {
        $fid = (int) $comment->forum_id;
        $matches = ((int) $contentRequest->referral_forum_id === $fid)
            || $contentRequest->referralTargets()->where('referral_forum_id', $fid)->exists();

        if (! $contentRequest->email || ! $matches) {
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
                'forumUrl' => url('forums/thread?id='.$fid),
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
     * When the requester posts via the public tracking link, notify assigned hub users and community leads.
     */
    public static function notifyParticipantsRequestorReplied(ContentRequest $contentRequest, ContentRequestReferralMessage $message): void
    {
        $contentRequest->loadMissing([
            'referredToUser',
            'referredToCommunity.creator',
            'referralTargets.user',
            'referralTargets.community.creator',
        ]);

        $hubDiscussUrl = route('content-request.referral.discuss', $contentRequest);
        $sent = [];

        foreach ($contentRequest->referralTargets as $target) {
            if ($target->user_id && $target->user?->email) {
                $to = $target->user->email;
                if (isset($sent[$to])) {
                    continue;
                }
                $sent[$to] = true;
                SendMailJob::dispatch([
                    'to' => $to,
                    'subject' => 'The requester replied on a referred content request',
                    'title' => 'New reply from requester',
                    'body' => view('emails.content_request_requestor_replied', [
                        'contentRequest' => $contentRequest,
                        'message' => $message,
                        'discussUrl' => $hubDiscussUrl,
                    ])->render(),
                ])->onQueue('default');
            }
            if ($target->community_of_practice_id && $target->community?->creator?->email) {
                $to = $target->community->creator->email;
                if (isset($sent[$to])) {
                    continue;
                }
                $sent[$to] = true;
                SendMailJob::dispatch([
                    'to' => $to,
                    'subject' => 'The requester replied on a community content request',
                    'title' => 'New reply from requester',
                    'body' => view('emails.content_request_requestor_replied', [
                        'contentRequest' => $contentRequest,
                        'message' => $message,
                        'discussUrl' => $contentRequest->discussionUrlForCommunity((int) $target->community_of_practice_id),
                    ])->render(),
                ])->onQueue('default');
            }
        }

        if ($contentRequest->referralTargets->isNotEmpty()) {
            return;
        }

        if ($contentRequest->referral_type === 'user' && $contentRequest->referredToUser?->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->referredToUser->email,
                'subject' => 'The requester replied on a referred content request',
                'title' => 'New reply from requester',
                'body' => view('emails.content_request_requestor_replied', [
                    'contentRequest' => $contentRequest,
                    'message' => $message,
                    'discussUrl' => $hubDiscussUrl,
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
                    'discussUrl' => $contentRequest->discussionUrlForCommunity((int) $contentRequest->referred_to_community_id),
                ])->render(),
            ])->onQueue('default');
        }
    }
}
