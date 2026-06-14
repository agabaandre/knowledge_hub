<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityComment extends Model
{
    use HasFactory;

    protected $appends = ['comment_replies', 'attachments'];

    public function getCommentRepliesAttribute()
    {
        return CommunityComment::where('parent_id', $this->id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function communityOfPractice()
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAttachmentsAttribute()
    {
        if (! $this->id) {
            return collect([]);
        }

        return CustomAttachment::where('model', 'community_comments')
            ->where('record_id', $this->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function attachmentsRelation()
    {
        return $this->hasMany(CustomAttachment::class, 'record_id', 'id')
            ->where('model', 'community_comments');
    }

    public function likes()
    {
        return $this->hasMany(CommunityCommentLike::class, 'community_comment_id');
    }

    public function isLikedBy($userId = null)
    {
        if (! $userId) {
            $userId = auth()->id();
        }

        return $userId ? $this->likes()->where('user_id', $userId)->exists() : false;
    }

    public function replies()
    {
        return $this->hasMany(CommunityComment::class, 'parent_id');
    }
}
