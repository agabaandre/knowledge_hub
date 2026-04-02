<?php

namespace App\Services;

use App\Jobs\NotifyCommunityMembers;
use App\Models\CommunityOfPracticeMembers;
use App\Models\ContentRequest;
use App\Models\Forum;
use App\Models\ForumCommunityOfPractice;
use App\Models\ForumEngagement;
use App\Models\ForumSubscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\Support\Str;

class ContentRequestReferralForumService
{
    /**
     * Remove the request record’s email (and common HTML-entity variants) from free text so it is not shown to the community.
     */
    public static function redactRequestorIdentifiersFromText(ContentRequest $contentRequest, string $text): string
    {
        $email = trim((string) $contentRequest->email);
        if ($email === '') {
            return $text;
        }

        $repl = '[contact details withheld]';
        $variants = array_unique(array_filter([
            $email,
            str_replace('@', '&#64;', $email),
            str_replace('@', '&#x40;', $email),
            str_replace('.', '&#46;', $email),
            htmlentities($email, ENT_QUOTES, 'UTF-8'),
        ]));

        foreach ($variants as $v) {
            if ($v !== '') {
                $text = str_ireplace($v, $repl, $text);
            }
        }

        return $text;
    }

    /**
     * Create an approved community-scoped forum thread for a content request referred to a CoP.
     * Subscribes all approved community members so they can comment without manually joining.
     */
    public static function createForCommunityReferral(ContentRequest $contentRequest, int $actorUserId): ?Forum
    {
        if ($contentRequest->referral_type !== 'community' || ! $contentRequest->referred_to_community_id) {
            return null;
        }

        if (! User::query()->whereKey($actorUserId)->exists()) {
            return null;
        }

        try {
            return DB::transaction(function () use ($contentRequest, $actorUserId) {
                $contentRequest->loadMissing(['referredToCommunity', 'country']);

                $now = now();

                $title = trim((string) $contentRequest->subject);
                if ($title === '') {
                    $title = 'Content request discussion';
                }
                $title = self::redactRequestorIdentifiersFromText($contentRequest, $title);
                if (! Str::startsWith(Str::lower($title), 'community content request')) {
                    $title = 'Community content request: '.$title;
                }
                $title = clean_unicode(Str::limit($title, 500));

                $communityName = $contentRequest->referredToCommunity->community_name ?? 'your community';

                $intro = '<div class="referral-forum-intro border rounded p-3 mb-3 bg-light">';
                $intro .= '<p class="mb-2"><strong>Community-linked content request</strong></p>';
                $intro .= '<p class="mb-2">The Knowledge Hub team referred a <strong>content request</strong> to <strong>'.e($communityName).'</strong> so members can discuss the topic here and share guidance or resources.</p>';
                $intro .= '<ul class="mb-2 pl-3"><li><strong>Requester privacy:</strong> The submitter’s name, email, and other contact details are <strong>not</strong> shown to the community. Do not post or guess their identity in comments.</li>';
                $intro .= '<li><strong>Topic below:</strong> The text under “Request details” is what they submitted about the subject matter (contact details may be removed automatically).</li></ul>';
                $intro .= '<p class="mb-0 small text-muted">Reply in the comments to help clarify the subject and suggest publications, tools, or next steps.</p>';
                $intro .= '</div>';

                $descHtml = (string) $contentRequest->description;
                $descHtml = self::redactRequestorIdentifiersFromText($contentRequest, $descHtml);

                $body = $intro;
                $body .= '<h3 class="h5 mt-3">Request details (subject matter)</h3>';
                $body .= '<div class="content-request-body">'.$descHtml.'</div>';

                if ($contentRequest->referral_notes) {
                    $notes = self::redactRequestorIdentifiersFromText($contentRequest, (string) $contentRequest->referral_notes);
                    $body .= '<h3 class="h5 mt-3">Notes from the hub team</h3>';
                    $body .= '<p>'.nl2br(e($notes)).'</p>';
                }

                if ($contentRequest->country) {
                    $body .= '<p class="small text-muted mb-0"><strong>Regional context (topic):</strong> '.e($contentRequest->country->name).'</p>';
                }

                $forum = new Forum;
                $forum->forum_title = $title;
                $forum->forum_description = sanitize_rich_text_for_storage(clean_unicode($body));
                $forum->created_by = $actorUserId;
                $forum->status = '1';
                $forum->is_approved = 1;
                $forum->is_rejected = 0;
                $forum->created_at = $now;
                $forum->updated_at = $now;

                if (DBSchema::hasColumn('forums', 'approved_by')) {
                    $forum->approved_by = $actorUserId;
                }
                if (DBSchema::hasColumn('forums', 'rejected_by')) {
                    $forum->rejected_by = null;
                }
                if (DBSchema::hasColumn('forums', 'is_resubmission_pending')) {
                    $forum->is_resubmission_pending = 0;
                }

                $forum->save();

                $link = new ForumCommunityOfPractice;
                $link->forum_id = $forum->id;
                $link->community_of_practice_id = (int) $contentRequest->referred_to_community_id;
                $link->save();

                ForumEngagement::incrementForumPost($actorUserId);

                $memberIds = CommunityOfPracticeMembers::query()
                    ->where('community_of_practice_id', $contentRequest->referred_to_community_id)
                    ->where('is_approved', 1)
                    ->pluck('user_id')
                    ->unique()
                    ->filter();

                foreach ($memberIds as $uid) {
                    ForumSubscription::firstOrCreate(
                        ['user_id' => (int) $uid, 'forum_id' => $forum->id],
                        []
                    );
                }

                $forum->load('user');
                NotifyCommunityMembers::dispatch(
                    [(int) $contentRequest->referred_to_community_id],
                    'forum',
                    $forum->id,
                    $forum->forum_title ?? 'Community discussion',
                    strip_tags(Str::limit($forum->forum_description ?? '', 400)),
                    $forum->user->name ?? User::query()->whereKey($actorUserId)->value('name') ?? 'Knowledge Hub'
                )->onQueue('default');

                return $forum;
            });
        } catch (\Throwable $e) {
            Log::error('ContentRequestReferralForumService: failed to create forum', [
                'content_request_id' => $contentRequest->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function forumThreadUrl(?int $forumId): string
    {
        if (! $forumId) {
            return '';
        }

        return url('forums/thread?id='.$forumId);
    }
}
