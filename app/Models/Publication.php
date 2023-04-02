<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $table = "publication";
    protected $appends = ['theme','label','value','versioning','is_favourite'];

    public function file_type(){
        return $this->belongsTo(PublicationType::class,"file_type_id","id");
    }

    public function attachments(){
        return $this->hasMany(PublicationAttachment::class);
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
        $fav = Favourite::where('user_id',current_user()->id)->where('publication_id',$this->id)->first();
        return ($fav)?true:false;
    }
}
