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
        'langauge',
        'theme_preference',
        'first_name',
        'last_name',
        'country_id',
        'phone_number',
        'job_title',
        'organization_name',
        'orcid',
        'is_photo_external',
        'photo',
        'author_id',
        'access_level_id',
        'is_subscribed',
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
        // Check if photo already contains full URL to prevent double paths
        if (strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0 || strpos($photo, url('/')) !== false) {
            return $photo;
        }
        return storage_link('uploads/users/'.$photo);
    }

    public function communities(){
        return $this->hasManyThrough(CommunityOfPractice::class,CommunityOfPracticeMembers::class,"user_id","id","id","community_of_practice_id");
    }

    public function preferences(){
        return $this->hasMany(UserPreference::class, 'user_id');
    }

    public function badges(){
        return $this->hasMany(UserBadge::class, 'user_id')->with('badgeType');
    }

    public function lifetimeBadge()
    {
        return $this->hasOne(UserLifetimeBadge::class, 'user_id')->with('badgeType');
    }

    public function communityMonthlyContributions()
    {
        return $this->hasMany(UserCommunityMonthlyContribution::class, 'user_id')->with('community');
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Send password reset email via queue system (which uses Exchange)
        try {
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $this->email,
            ]);
            
            $mailData = [
                'email' => $this->email,
                'subject' => 'Reset Your Password - Africa CDC Knowledge Hub',
                'body' => view('emails.password_reset', [
                    'name' => $this->name,
                    'token' => $token,
                    'email' => $this->email,
                    'resetUrl' => $resetUrl,
                ])->render(),
                'title' => 'Reset Your Password'
            ];
            
            \App\Jobs\SendMailJob::dispatch($mailData)->onQueue('default');
        } catch (\Exception $e) {
            \Log::error('Exception queuing password reset notification: ' . $e->getMessage(), [
                'user_id' => $this->id,
                'email' => $this->email,
                'exception' => get_class($e)
            ]);
        }
    }

}
