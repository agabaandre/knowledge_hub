<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Region;
use App\Models\Country;

class CommunityOfPractice extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_name',
        'description',
        'created_by',
        'is_active',
        'region_id',
        'country_id',
        'organisation',
        'department',
        'is_public'
    ];

    // Specify the attributes to append
    protected $appends = [
        'publications_count',
        'forums_count',
        'members_count',
        'user_joined',
        'user_pending_approval'
    ];

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

    // Define the relationship with invitations
    public function invitations()
    {
        return $this->hasMany(CommunityInvitation::class, 'community_of_practice_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'community_of_practice_tags', 'community_of_practice_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'community_of_practice_id')
            ->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(CommunityComment::class, 'community_of_practice_id');
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

    // Accessor to get the count of approved members
    public function getMembersCountAttribute()
    {
        return $this->approvedMembers()->count();
    }

    public function getUserJoinedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return CommunityOfPracticeMembers::where('user_id', auth()->id())
            ->where('community_of_practice_id', $this->id)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->exists();
    }

    public function getUserPendingApprovalAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        $isInMCommunity = CommunityOfPracticeMembers::where('user_id', auth()->id())
            ->where('community_of_practice_id', $this->id)
            ->where('is_approved', 0)
            ->exists();

        return $isInMCommunity;
    }

}
