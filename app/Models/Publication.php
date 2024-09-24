<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $table = "publication";
    protected $guarded =[];
    protected $appends = ['theme','label','value','is_favourite','approved_comments','pending_comments','has_attachments','tag_ids','image_url'];

    public function file_type(){
        return $this->belongsTo(PublicationType::class,"file_type_id","id");
    }

    public function category(){
        return $this->belongsTo(PublicationCategory::class,"publication_catgory_id","id");
    }

    public function data_category(){
        return $this->belongsTo(DataCategory::class, "data_category_id","id");
    }

    public function attachments(){
        return $this->hasMany(PublicationAttachment::class);
    }

    public function getHasAttachmentsAttribute(){
        return count($this->attachments)>0;
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

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $theme = cache()->remember('Theme'.$this->id,$minutes, function () {
            return  @$this->sub_theme->theme;
        });

        return $theme;
    }

    public function getLabelAttribute(){
        return substr(cleanUTF8($this->title),0,100);
    }


    public function getValueAttribute(){
        return substr(cleanUTF8($this->title),0,100);
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

    public function accessgroups(){

        return $this->hasManyThrough(
            UserAccessGroup::class,
            PublicationAccessGroup::class,
            'publication_id', // Foreign key on PublicationAccessGroup table.
            'id', // Foreign key on UserAccessGroup table. Assuming 'id' is the primary key.
            'id', // Local key on Publication table.
            'user_access_group_id' // Local key on PublicationAccessGroup table that relates to UserAccessGroup.
        );
    }

    public function communities(){
       
        return $this->hasManyThrough(
            CommunityOfPractice::class, //get access to these
            PublicationCommunityOfPractice::class, //thru these
            'publication_id', // Foreign key on PublicationCommunityOfPractice table.
            'id', // Foreign key on UserAccessGroup table. Assuming 'id' is the primary key.
            'id', // Local key on Publication table.
            'community_of_practice_id' // Local key on PublicationCommunityOfPractice table that relates to UserAccessGroup.
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function favourited(){
        return $this->hasMany(Favourite::class);
    }


    public function getTagIdsAttribute(){
        $tag_ids = PublicationTag::where('publication_id',$this->id)
        ->get()->pluck('id');
        return $tag_ids->toArray();
    }

    public function country()
    {

       return  $this->belongsTo(Country::class,"geographical_coverage_id","id");
    }

    public function getImageUrlAttribute(){
        return ($this->cover_is_exteranl)?$this->cover:storage_link('uploads/publications/'.$this->cover);
    }

    public function getPublicationAttribute($value)
    {
        return cleanUTF8($value);
    }

    // Ensure the content is UTF-8 encoded
    public function getDescriptionAttribute($value)
    {
        return  cleanUTF8($value);
    }

    public function getTitleAttribute($value)
    {
        return cleanUTF8($value);
    }
    


}
