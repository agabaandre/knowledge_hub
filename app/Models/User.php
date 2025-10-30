<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable 
{
    use  HasFactory, Notifiable,HasRoles, HasApiTokens, Notifiable;

   // protected $table = "user";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_photo_external' => 'integer',
    ];

    protected $appends = ["names"];
    

    public function getNamesAttribute(){
        return ($this->firstname)?$this->firstname." ".$this->lastname:$this->name;
    }
    

    public function country(){
        return $this->belongsTo(Country::class);
     }

     public function author(){
        return $this->belongsTo(Author::class);
     }

     /**
      * Get the area attribute - not automatically appended to prevent memory issues
      * Access via $user->area when needed
      */
     public function getAreaAttribute(){
        if (!$this->country_id) {
            return null;
        }
        
        // Use the country relationship to get area
        $country = $this->country;
        if (!$country || !$country->name) {
            return null;
        }
        
        return GeoCoverage::where('name','like','%'.$country->name.'%')->first();
     }

     public function getPhotottribute(){
        return (intval($this->is_photo_external)==1)?$this->photo:user_profile_photo($this->photo);
     }

     public function access_level(){
        return $this->belongsTo(AccessLevel::class,"access_level_id","id");
     }

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getPhotoAttribute($photo){
        if (empty($photo)) {
            return null;
        }
        // If photo is an external URL (set by social login), return as-is
        if (!empty($this->attributes['is_photo_external']) && intval($this->attributes['is_photo_external']) === 1) {
            return $photo;
        }
        return storage_link('uploads/users/'.$photo);
    }

    public function communities(){
        return $this->hasManyThrough(CommunityOfPractice::class,CommunityOfPracticeMembers::class,"user_id","id","id","community_of_practice_id");
    }

     /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function preferences()
    {
        return $this->hasManyThrough(
            SubThemeticArea::class,
            UserPreference::class,
            'user_id', // Foreign key on UserPreference table
            'id', // Foreign key on SubThemeticArea table
            'id', // Local key on User table
            'subtheme_id' // Local key on UserPreference table
        );
    }

}
