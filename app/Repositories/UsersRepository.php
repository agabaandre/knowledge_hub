<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\User;
use App\Models\UserPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;
use Laravel\Passport\Token;
use App\Models\CommunityOfPracticeMembers;

class UsersRepository {

    

    public function save(Request $request,$is_social=false){

        $user = ($request->id)?User::find($request->id):new User();
        
        //don't update these values for social signups account edits
        if( (!$user->id || ($user->id && !$user->is_social_login))){

            $user->name          = $request->firstname." ".$request->lastname;
            $user->first_name    = $request->firstname;
            $user->last_name     = $request->lastname;
            $user->email         = $request->email;

        }

        $user->country_id    = ($request->country_id)?$request->country_id:$user->country_id;
        $user->phone_number  = ($request->phone)?$request->phone:$user->phone;
        $user->job_title     = ($request->job)?$request->job:$user->job; 
        $user->is_photo_external = ($request->is_photo_external)?$request->is_photo_external:$user->is_photo_external;

        if(!$is_social || ($user->id && !$user->is_social_login)){

            $user->password      = Hash::make($request->password);
            $token = Str::random(10);
            $user->verification_token = $token;
        }
        else{

            //for login
            if(!$user->id){
                $existing_user = $this->find_by_email($request->email,true);
                if($existing_user)
                    return $existing_user;
            }

            //else, it's signup or account edit
             
            $user->is_social_login    = 1;
            $user->social_provider    = $request->social_provider;
            if($request->photo)
                $user->photo = $request->photo;

        }
        
        if($request->hasFile( 'photo')):
            //upload photo
            $file        = $request->file('photo');
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
            $file->move(storage_path().'/app/public/uploads/users/',$file_path);
            $user->photo  = $file_path;
            $user->is_photo_external = 0;
        endif;

        if($request->subscribe)
        $user->is_subscribed     = ($request->subscribe=="on")?true:false;


        $user->save();
        $user = User::find($user->id);

        if(!$user->author_id)
        $user->author()->create(['name'=>$user->name]);

        if(!$is_social)
        $this->send_email($request, $token);

        if($request->preferences){

            $user->preferences()->delete();
            @$this->save_preferences($user->id,$request->preferences);
        }

        if($request->communities){

            //$user->communities()->delete();
            //@$this->save_communities($user->id,$request->communities);
        }
        
        return $user;
    }

    public function send_email($request, $token){

        $mail['subject'] = 'Account confirmation';
        $mail['email'] = $request->email;
        $mail['body'] = view('emails.email_verification', ['token' => $token])->render();

        SendMailJob::dispatch($mail);
    }

    public function save_preferences($user_id,$preferences){

        $preferences = is_array($preferences) ? $preferences : (json_decode($preferences) ?? []);

        foreach($preferences as $subtheme_id){

            $pref = new UserPreference();
            $pref->user_id = $user_id;
            $pref->subtheme_id  = $subtheme_id;
            $pref->save();

        }

    }

    public function save_communities($user_id,$communities){

        $communities = is_array($communities) ? $communities : (json_decode($communities) ?? []);

        foreach($communities as $community_id){

            $comm = new CommunityOfPracticeMembers();
            $comm->user_id = $user_id;
            $comm->community_of_practice_id  = $community_id;
            $comm->save();

        }

    }

    public function user_by_token($token){

        return User::where('verification_token',$token)->first();
    }

    public function verify_account($request){
        
        $user = $this->user_by_token($request->t);

        if($user):
        $user->is_verified = 1;
        $user->verification_token = 0;
        $user->email_verified_at  = Carbon::now();
        return $user->update();
        else:
            return null;
        endif;
    }

    public function update_profile(Request $request){
        
        $user = User::find($request->id);

        if($request->firstname)
        $user->first_name = $request->firstname;

        if($request->lastname)
        $user->last_name  = $request->lastname;

        if($request->email)
        $user->email      = $request->email;

        if($request->langauge)
        $user->langauge = $request->langauge;

        if($request->firstname && $request->lastname)
        $user->name  = $request->firstname." ".$request->lastname;
       
        if($request->phone_number)
        $user->phone_number      = $request->phone_number;

        if($request->level_id)
        $user->access_level_id = $request->level_id;

        \Log::info("preferences::");
        \Log::info($request->all());

        if($request->preferences){

            $user->preferences()->delete();
            $preferences = is_array($request->preferences) ? $request->preferences : (json_decode($request->preferences) ?? []);
            //$user->preferences()->attach($preferences);
            $this->save_preferences($request->id,$request->preferences);
        }

        if($request->hasFile('photo')):
            //upload photo
            $file        = $request->file('photo');
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
            $file->move(storage_path().'/app/public/uploads/users/',$file_path);
            $user->photo  = $file_path;
        endif;

        $user->update();

        return $user;
    }

    public function update_password(Request $request){

        $user = User::find(current_user()->id);
        $newpass = Hash::make($request->password);
        $user->password = $newpass;
        $user->is_changed = 1;

        return $user->update();
    }

    public function logout(User $user)
    {
        // Revoke all tokens for the user
        $user->tokens->each(function ($token) {
            $token->revoke();
        });

        // Clear the FCM token
        $user->fcm_token = null;
        $user->save();

        return true;
    }

    public function profile(Request $request){
        return User::find($request->id);
    }

    public function find_by_email($email,$as_social=false){
       
        $qry = User::where('email',$email);
        
        if($as_social):
            $qry->where('is_social_login',true);
        else:
            $qry->where('is_social_login',false);
        endif;

        $user = $qry->first();
        return $user;
    }


  


}
