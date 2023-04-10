<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    public function comments(){
        return $this->hasMany(ForumComment::class);
    }

    public function tags(){
        return $this->hasMany(ForumTag::class);
    }

    public function user(){
       return  $this->belongsTo(User::class,"created_by","id");
    }
}
