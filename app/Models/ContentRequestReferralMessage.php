<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentRequestReferralMessage extends Model
{
    protected $fillable = [
        'content_request_id',
        'user_id',
        'posted_via_track',
        'body',
    ];

    protected $casts = [
        'posted_via_track' => 'boolean',
    ];

    public function contentRequest(): BelongsTo
    {
        return $this->belongsTo(ContentRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authorLabel(): string
    {
        if ($this->posted_via_track) {
            return 'Requester';
        }
        if ($this->user) {
            return $this->user->name;
        }

        return 'Participant';
    }
}
