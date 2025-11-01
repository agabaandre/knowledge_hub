<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationTag extends Model
{
    use HasFactory;

    protected $table = 'publication_tags';
    
    public $timestamps = false;
    
    protected $fillable = ['tag_id', 'publication_id'];

    public function tag(){
        return $this->belongsTo(Tag::class,"tag_id","id");
    }

    public function getTagTextAtrribute(){

        return $this->tag->tag_text;
    }
}
