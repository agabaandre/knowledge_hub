<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationTag extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function tag(){
        return $this->belongsTo(Tag::class,"tag_id","id");
    }

    public function getTagTextAtrribute(){

        return $this->tag->tag_text;
    }
}
