<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Timestamps are manual ($timestamps = false) but columns exist; cast so views and APIs
     * can use Carbon helpers (e.g. diffForHumans()) on created_at / updated_at.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['attachments'];

    public function comments(){
        return $this->hasMany(ForumComment::class);
    }

    public function tags(){
        return $this->hasMany(ForumTag::class);
    }

    public function user(){
       return  $this->belongsTo(User::class,"created_by","id");
    }

    public function likes(){
        return $this->hasMany(ForumLike::class);
    }

    public function isLikedBy($userId = null){
        if (!$userId) {
            $userId = auth()->id();
        }
        return $userId ? $this->likes()->where('user_id', $userId)->exists() : false;
    }

    /**
     * Forums awaiting moderator approval (not rejected).
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', 0)
            ->where('status', 0)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            });
    }

    public function communities(){
       
        return $this->hasManyThrough(
            CommunityOfPractice::class, //get access to these
            ForumCommunityOfPractice::class, //thru these
            'forum_id', // Foreign key on ForumCommunityOfPractice table.
            'id', // Foreign key on UserAccessGroup table. Assuming 'id' is the primary key.
            'id', // Local key on Forum table.
            'community_of_practice_id' // Local key on ForumCommunityOfPractice table that relates to UserAccessGroup.
        );
    }

    public function getForumImageAttribute($image){
        return storage_link('uploads/forums/'.$image);
    }

    public function getAttachmentsAttribute(){

        DB::enableQueryLog();

        return CustomAttachment::where('model','forums')->where('record_id',$this->id)->get();
    }
}
