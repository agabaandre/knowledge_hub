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

    public function communityPublications()
    {
        return $this->hasMany(PublicationCommunityOfPractice::class, 'community_of_practice_id');
    }

    // Define the relationship with community forums
    public function communityForums()
    {
        return $this->hasMany(ForumCommunityOfPractice::class, 'community_of_practice_id');
    }

    // Accessor to get the count of publications
    public function getPublicationsCountAttribute()
    {
        return $this->communityPublications()->count();
    }

    // Accessor to get the count of forums
    public function getForumsCountAttribute()
    {
        return $this->communityForums()->count();
    }

    // Accessor to get the count of members
    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }
}
