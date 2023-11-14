<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;

class UsersRepository {

    

    public function save(Request $request){

        $user = new User();
        $user->name          = $request->firstname." ".$request->lastname;
        $user->first_name    = $request->firstname;
        $user->last_name      = $request->lastname;
        $user->email         = $request->email;
        $user->country_id    = $request->country_id;
        $user->phone_number  = $request->phone;
        $user->job_title     = $request->job; 
        $user->password      = Hash::make($request->password);

        $token = Str::random(10);
        $user->verification_token =$token; 

        if($request->subscribe)
        $user->is_subscribed     = ($request->subscribe=="on")?true:false;

        $user->save();

        $this->send_email($request, $token);

        @$this->save_preferences($user->id,$request->preferences);
        
        return $user;
    }

    public function send_email($request, $token){

        $mail['body'] = 'Account confirmation';
        $mail['email'] = $request->email;
        $mail['body'] = view('emails.email_verification', ['token' => $token])->render();

        SendMailJob::dispatch($mail);
    }

    public function save_preferences($user_id,$preferences){

        foreach($preferences as $key=>$value){

            $pref = new UserPreference();
            $pref->user_id = $user_id;
            $pref->tag_id  = $value;
            $pref->save();

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
        return $user->update();
        else:
            return null;
        endif;
    }

    public function update_profile(Request $request){
        
        $user = User::find(current_user()->id);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->name  = $request->first_name." ".$request->last_name;
        if($request->phone_number)
        $user->phone_number      = $request->phone_number;

        if($request->preferences){

            $this->save_preferences(current_user()->id,$request->preferences);
        }

        $user->update();

        return $user->update();
    }

    public function update_password(Request $request){

        $user = User::find(current_user()->id);
        $newpass = Hash::make($request->password);
        $user->password = $newpass;
        $user->is_changed = 1;

        return $user->update();
    }

  


}