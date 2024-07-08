<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  HasFactory, Notifiable,HasRoles;

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

    protected $appends = ["names","area"];

    public function getNamesAttribute(){
        return ($this->firstname)?$this->firstname." ".$this->lastname:$this->name;
    }

    public function preferences(){
       return $this->hasMany(UserPreference::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
     }

     public function getAreaAttribute(){
        return ($this->country)?GeoCoverage::where('name','like','%'.$this->country->name.'%')->first():null;
     }

     public function access_level(){
        return $this->belongsTo(AccessLevel::class,"access_level_id","id");
     }

}
