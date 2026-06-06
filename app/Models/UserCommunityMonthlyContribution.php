<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCommunityMonthlyContribution extends Model
{
    protected $fillable = [
        'user_id',
        'community_of_practice_id',
        'year',
        'month',
        'contributions_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }
}
