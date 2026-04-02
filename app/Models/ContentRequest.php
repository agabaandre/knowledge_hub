<?php

namespace App\Models;

use App\Services\ContentRequestReferralForumService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function isProcessed(): bool
    {
        return ! is_null($this->processed_at);
    }

    public function isReferred(): bool
    {
        return ! empty($this->referral_type) && ! is_null($this->referred_at);
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
        if (! empty($this->referral_forum_id)) {
            return ContentRequestReferralForumService::forumThreadUrl((int) $this->referral_forum_id);
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

        if ($this->referral_type !== 'community' || ! $this->referred_to_community_id) {
            return false;
        }

        $community = $this->referredToCommunity;
        if (! $community) {
            $community = CommunityOfPractice::query()->find($this->referred_to_community_id);
        }
        if (! $community) {
            return false;
        }

        if ((int) $community->created_by === (int) $user->id) {
            return true;
        }

        $membership = CommunityOfPracticeMembers::query()
            ->where('community_of_practice_id', $this->referred_to_community_id)
            ->where('user_id', $user->id)
            ->where('is_approved', 1)
            ->first();

        return $membership && ! empty($membership->is_admin);
    }
}
