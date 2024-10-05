<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityOfPractice extends Model
{
    use HasFactory;

    public function members()
    {
        return $this->hasMany(User::class, 'community_of_practice_members', 'community_of_practice_id', 'user_id');
    }

    public function membership()
    {
        return $this->hasMany(CommunityOfPracticeMembers::class, 'community_of_practice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'community_of_practice_members', 'community_of_practice_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Define a scope for approved members
    public function approvedMembers()
    {
        return $this->membership()->where('is_approved', 1);
    }

    // Define a scope for pending members
    public function pendingMembers()
    {
        return $this->membership()->where('is_approved', 0);
    }

    // Define a scope for rejected members
    public function rejectedMembers()
    {
        return $this->membership()->where('is_approved', 2);
    }
}
