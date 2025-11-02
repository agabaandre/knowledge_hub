<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $appends =['comment_replies','attachments'];
    // Timestamps enabled - created_at and updated_at are now tracked

    public function getCommentRepliesAttribute(){
         return ForumComment::where('parent_id',$this->id)->get();
    }

    public function user(){
        return $this->belongsTo(User::class,"created_by","id");
    }

    /**
     * Get attachments for this comment from custom_attachments table
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAttachmentsAttribute(){
        if (!$this->id) {
            return collect([]);
        }
        
        return CustomAttachment::where('model', 'forum_comments')
            ->where('record_id', $this->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    /**
     * Relationship method for attachments (alternative to accessor)
     * This can be used for eager loading if needed in the future
     */
    public function attachmentsRelation(){
        return $this->hasMany(CustomAttachment::class, 'record_id', 'id')
            ->where('model', 'forum_comments');
    }

    public function likes(){
        return $this->hasMany(ForumCommentLike::class, 'forum_comment_id');
    }

    public function isLikedBy($userId = null){
        if (!$userId) {
            $userId = auth()->id();
        }
        return $userId ? $this->likes()->where('user_id', $userId)->exists() : false;
    }

    public function replies(){
        return $this->hasMany(ForumComment::class, 'parent_id');
    }
}
