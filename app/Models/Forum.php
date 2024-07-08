<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function comments(){
        return $this->hasMany(ForumComment::class);
    }

    public function tags(){
        return $this->hasMany(ForumTag::class);
    }

    public function user(){
       return  $this->belongsTo(User::class,"created_by","id");
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
}
