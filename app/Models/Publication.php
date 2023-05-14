<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $table = "publication";
    protected $appends = ['theme','label','value','is_favourite','approved_comments','pending_comments'];

    public function file_type(){
        return $this->belongsTo(PublicationType::class,"file_type_id","id");
    }

    public function attachments(){
        return $this->hasMany(PublicationAttachment::class);
    }

    public function tags(){
        return $this->hasMany(PublicationTag::class);
    }

    public function author(){
        return $this->belongsTo(Author::class);
    }

    public function sub_theme(){
        return $this->belongsTo(SubThemeticArea::class,"sub_thematic_area_id","id");
    }


    public function getThemeAttribute(){
        return $this->sub_theme->theme;
    }

    public function getLabelAttribute(){
        return substr($this->title,0,100);
    }


    public function getValueAttribute(){
        return substr($this->title,0,100);
    }


    public function comments(){
        return $this->hasMany(PublicationComment::class);
    }

    public function versioning(){
        return $this->hasMany(Publication::class,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo(Publication::class,'id','parent_id');
    }

    public function getIsFavouriteAttribute(){
        if(!current_user())
        return false;
        
        $fav = Favourite::where('user_id',current_user()->id)->where('publication_id',$this->id)->first();
        return ($fav)?true:false;
    }

    public function summaries(){
        return $this->hasMany(PublicationSummary::class,"resource_id");
    }

    public function getApprovedCommentsAttribute(){
        $comments = PublicationComment::where('publication_id',$this->id)
        ->where('status','approved')
        ->get();
        return $comments;
    }

    public function getPendingCommentsAttribute(){
        $comments = PublicationComment::where('publication_id',$this->id)
        ->where('status','pending')
        ->get();
        return $comments;
    }
}
