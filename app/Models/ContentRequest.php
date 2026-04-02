<?php

namespace App\Models;

use App\Services\ContentRequestReferralForumService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ContentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'description',
        'country_id',
        'email',
        'processed_at',
        'processed_by',
        'content_links',
        'admin_comments',
        'referral_type',
        'referred_to_user_id',
        'referred_to_community_id',
        'referred_at',
        'referred_by',
        'referral_notes',
        'requestor_track_token',
        'referral_forum_id',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'referred_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function referredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_to_user_id');
    }

    public function referredToCommunity(): BelongsTo
    {
        return $this->belongsTo(CommunityOfPractice::class, 'referred_to_community_id');
    }

    public function referralMessages(): HasMany
    {
        return $this->hasMany(ContentRequestReferralMessage::class)->orderBy('created_at')->orderBy('id');
    }

    public function referralTargets(): HasMany
    {
        return $this->hasMany(ContentRequestReferralTarget::class);
    }

    /**
     * Distinct forum IDs linked to this referral (targets + legacy column).
     *
     * @return Collection<int, int>
     */
    public function referralForumIds(): Collection
    {
        $ids = $this->referralTargets()
            ->whereNotNull('referral_forum_id')
            ->pluck('referral_forum_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if (! empty($this->referral_forum_id) && ! $ids->contains((int) $this->referral_forum_id)) {
            $ids->push((int) $this->referral_forum_id);
        }

        return $ids->unique()->values();
    }

    public function communityForReferralForum(int $forumId): ?CommunityOfPractice
    {
        $target = $this->referralTargets
            ->firstWhere('referral_forum_id', $forumId);
        if ($target && $target->community_of_practice_id) {
            return $target->community ?? CommunityOfPractice::query()->find($target->community_of_practice_id);
        }
        if ((int) $this->referral_forum_id === $forumId && $this->referred_to_community_id) {
            return $this->referredToCommunity ?? CommunityOfPractice::query()->find($this->referred_to_community_id);
        }

        return null;
    }

    /**
     * Forum thread URL for a given community when this request was referred to multiple CoPs.
     */
    public function discussionUrlForCommunity(int $communityId): string
    {
        $target = $this->referralTargets()
            ->where('community_of_practice_id', $communityId)
            ->whereNotNull('referral_forum_id')
            ->first();
        if ($target) {
            return ContentRequestReferralForumService::forumThreadUrl((int) $target->referral_forum_id);
        }
        if ((int) $this->referred_to_community_id === $communityId && $this->referral_forum_id) {
            return ContentRequestReferralForumService::forumThreadUrl((int) $this->referral_forum_id);
        }

        return $this->discussionUrl();
    }

    public function isProcessed(): bool
    {
        return ! is_null($this->processed_at);
    }

    public function isReferred(): bool
    {
        return ! empty($this->referral_type) && ! is_null($this->referred_at);
    }

    public function processingMethodLabel(): string
    {
        $this->loadMissing('referralTargets');

        $hasCommunityTarget = $this->referralTargets->whereNotNull('community_of_practice_id')->isNotEmpty()
            || ! empty($this->referred_to_community_id)
            || in_array((string) $this->referral_type, ['community', 'mixed'], true);

        if ($hasCommunityTarget) {
            return 'Sent to community';
        }

        $hasUserTarget = $this->referralTargets->whereNotNull('user_id')->isNotEmpty()
            || ! empty($this->referred_to_user_id)
            || (string) $this->referral_type === 'user';

        if ($hasUserTarget) {
            return 'Sent to subject matter expert user';
        }

        return 'Processed by admin';
    }

    public function trackUrl(): string
    {
        if (empty($this->requestor_track_token)) {
            return '';
        }

        return url('/content-request/track/'.$this->requestor_track_token);
    }

    public function discussionUrl(): string
    {
        $forumId = $this->referralTargets()
            ->whereNotNull('referral_forum_id')
            ->value('referral_forum_id');

        if (empty($forumId) && ! empty($this->referral_forum_id)) {
            $forumId = $this->referral_forum_id;
        }

        if (! empty($forumId)) {
            return ContentRequestReferralForumService::forumThreadUrl((int) $forumId);
        }

        return url('/content-request/referral/'.$this->id.'/discuss');
    }

    /**
     * Logged-in hub users who may post in the referral discussion (assignee, CoP member, or content-request staff).
     */
    public function userMayParticipateInReferralDiscussion(?User $user): bool
    {
        if (! $user || ! $this->isReferred()) {
            return false;
        }

        if ($user->can('manage_content_requests')) {
            return true;
        }

        $this->loadMissing('referralTargets');

        if ($this->referralTargets->contains(fn ($t) => (int) $t->user_id === (int) $user->id)) {
            return true;
        }

        $communityIds = $this->referralTargets
            ->pluck('community_of_practice_id')
            ->filter()
            ->unique()
            ->all();

        if ($this->referred_to_community_id && ! in_array((int) $this->referred_to_community_id, array_map('intval', $communityIds), true)) {
            $communityIds[] = (int) $this->referred_to_community_id;
        }

        foreach ($communityIds as $cid) {
            if (CommunityOfPracticeMembers::query()
                ->where('community_of_practice_id', $cid)
                ->where('user_id', $user->id)
                ->where('is_approved', 1)
                ->exists()) {
                return true;
            }
        }

        if ($this->referral_type === 'user' && (int) $this->referred_to_user_id === (int) $user->id) {
            return true;
        }

        if ($this->referral_type === 'community' && $this->referred_to_community_id) {
            return CommunityOfPracticeMembers::query()
                ->where('community_of_practice_id', $this->referred_to_community_id)
                ->where('user_id', $user->id)
                ->where('is_approved', 1)
                ->exists();
        }

        return false;
    }

    /**
     * Who may mark this referral as processed (hub workflow): system admins / content-request managers,
     * or community admins for requests referred to their community.
     */
    public function userMayMarkReferralAsProcessed(?User $user): bool
    {
        if (! $user || ! $this->isReferred() || $this->isProcessed()) {
            return false;
        }

        if (is_admin()) {
            return true;
        }

        if ($user->can('manage_content_requests')) {
            return true;
        }

        $this->loadMissing('referralTargets');

        $communityIds = $this->referralTargets
            ->pluck('community_of_practice_id')
            ->filter()
            ->unique()
            ->all();

        if ($this->referred_to_community_id && ! in_array((int) $this->referred_to_community_id, array_map('intval', $communityIds), true)) {
            $communityIds[] = (int) $this->referred_to_community_id;
        }

        foreach ($communityIds as $cid) {
            $community = CommunityOfPractice::query()->find($cid);
            if (! $community) {
                continue;
            }
            if ((int) $community->created_by === (int) $user->id) {
                return true;
            }
            $membership = CommunityOfPracticeMembers::query()
                ->where('community_of_practice_id', $cid)
                ->where('user_id', $user->id)
                ->where('is_approved', 1)
                ->first();
            if ($membership && ! empty($membership->is_admin)) {
                return true;
            }
        }

        return false;
    }
}
