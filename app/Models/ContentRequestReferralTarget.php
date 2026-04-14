<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentRequestReferralTarget extends Model
{
    protected $fillable = [
        'content_request_id',
        'user_id',
        'community_of_practice_id',
        'referral_forum_id',
    ];

    public function contentRequest(): BelongsTo
    {
        return $this->belongsTo(ContentRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }
}
