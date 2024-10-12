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
    ];

    protected $appends = ["names","area","settings"];
    

    public function getNamesAttribute(){
        return ($this->firstname)?$this->firstname." ".$this->lastname:$this->name;
    }

    public function getSettingsAttribute(){
        return settings();
    }
    
    public function preferences(){
       return $this->hasMany(UserPreference::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
     }

     public function author(){
        return $this->belongsTo(Author::class);
     }

     public function getAreaAttribute(){
        return ($this->country)?GeoCoverage::where('name','like','%'.$this->country->name.'%')->first():null;
     }

     public function getPhotottribute(){
        return user_profile_photo($this->photo);
     }

     public function access_level(){
        return $this->belongsTo(AccessLevel::class,"access_level_id","id");
     }

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getPhotoAttribute($photo){
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

}
