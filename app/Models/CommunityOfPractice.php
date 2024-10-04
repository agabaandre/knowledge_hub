<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityOfPractice extends Model
{
    use HasFactory;

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_of_practice_members', 'community_of_practice_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'community_of_practice_members', 'community_of_practice_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
}
