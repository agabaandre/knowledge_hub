<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;

class UsersRepository{

    

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

        $this->save_preferences($user->id,$request->preferences);

        // Mail::send('emails.email_verification', ['token' => $token], function($message) use($request){
        //     $message->to($request->email);
        //     $message->subject('Account Verification');
        // });

        return $user;
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

  


}