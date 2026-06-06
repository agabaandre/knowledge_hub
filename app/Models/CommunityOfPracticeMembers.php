<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityOfPracticeMembers extends Model
{
    use HasFactory;

    protected $fillable = ['community_of_practice_id', 'user_id', 'is_approved', 'is_admin', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }
}
