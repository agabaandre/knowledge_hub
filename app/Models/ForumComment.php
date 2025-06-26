<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $appends =['comment_replies','attachments'];
    public $timestamps = false;

    public function getCommentRepliesAttribute(){
         return ForumComment::where('parent_id',$this->id)->get();
    }

    public function user(){
        return $this->belongsTo(User::class,"created_by","id");
    }

    public function getAttachmentsAttribute(){

        return CustomAttachment::where('model', 'forum_comments')->where('record_id',$this->id)->get();
    }
}
