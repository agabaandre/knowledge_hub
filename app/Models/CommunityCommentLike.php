<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityCommentLike extends Model
{
    use HasFactory;

    protected $fillable = ['community_comment_id', 'user_id'];

    public function comment()
    {
        return $this->belongsTo(CommunityComment::class, 'community_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
