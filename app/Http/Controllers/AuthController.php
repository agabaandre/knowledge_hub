<?php

namespace App\Http\Controllers;

use App\Repositories\AuthorsRepository;
use App\Repositories\UsersRepository;
use App\Services\SocialLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private $usersRepo, $authorsRepo,$socialLoginService;

    public function __construct( UsersRepository $usersRepo, AuthorsRepository $authorsRepo,SocialLoginService $socialLoginService)
    {
        $this->usersRepo       = $usersRepo;
        $this->authorsRepo     = $authorsRepo;
        $this->socialLoginService = $socialLoginService;
    }

    public function register(Request $request){


        $request->validate([
            'firstname'=> ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response'=>'required' //|captcha
        ],[
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
            //'g-recaptcha-response.captcha' => 'Captcha verification failed, please try again.',
        ]);

        $saved = $this->usersRepo->save($request);
        
        $message = ($saved)?'Resgistration successful,Check Email to activate':'Request failed try again';
        $data['alert_class'] = ($saved)?'success':'danger';
        $data['alert']       = $message;

        return back()->with($data);
    }

    public function verifyAccount($token)
    {
        $verifiedUser = $this->usersRepo->user_by_token($token);
  
        $message = 'Sorry your email cannot be identified.';
  
        if(!is_null($verifiedUser) ){

            $user = $verifiedUser;
              
            if(!$user->email_verified_at) {

                $user->email_verified_at = 1;
                $user->save();

                $this->authorsRepo->save($user->name);
                $message = "Your e-mail is verified. You can now login.";

            } else {
                $message = "Your e-mail is already verified. You can continuee to login.";
            }
        }
  
      return redirect()->route('login')->with('message', $message);
    }

    
    public function update_profile(Request $request){

        $val_rules =[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'preferences'=>'required',
            'id'=>'required'
        ];

        $request->validate($val_rules);

        $saved   = $this->usersRepo->save($request);

        $message = ($saved)?'Profile update successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        return back()->with($data);
    }

    public function update_password(Request $request){

        $val_rules =[
            'old_pass'=>'required',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ];

        $credentials =['email'=>current_user()->email, 'password'=>$request->old_pass];

        if(Auth::attempt($credentials)){
            $saved   = $this->usersRepo->update_password($request);
        }else{
            $saved = false;
        }
        
        $message = ($saved)?'Password update successful':'Request failed, provided current password is incorrect';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        return back()->with($data);
    }

    public function microsoftLogin(){

        // Get the user from Microsoft
        $socialUser = Socialite::driver('microsoft')->user();
        // Convert the MicrosoftUser object to a standard object
        $user = json_decode(json_encode($socialUser));

        if($this->usersRepo->find_by_email($user->user->mail)){

            $data['alert_class'] = 'danger';
            $data['message']     = "User with this email exists and can login with username and password";
            $data['status']      = 200;
            return redirect('/login')->with($data);
        }

        $user =$this->socialLoginService->microsoftCallback($user);

        Auth::login($user);

        if(!$user->country_id){
            $data['alert_class'] = 'success';
            $data['message']     = "Please complete yur profile";
            $data['status']      = 200;
            $redirect_to = "/account";
         }else{
            $redirect_to ="/";
            $data = [];
         }

         return redirect($redirect_to)->with($data);
   }


    public function googleLogin(){

         // Get the user from Google
         $socialUser = Socialite::driver('google')->user();
        
         // Convert the GoogleUser object to a standard object
         $user = json_decode(json_encode($socialUser));
         $user_exists = $this->usersRepo->find_by_email($user->user->email);
        
         if($user_exists):
            $user = $user_exists;
         else:
            $user =$this->socialLoginService->googleCallback($user);
         endif;

         Auth::login($user);

         if(!$user->country_id){
            $data['alert_class'] = 'success';
            $data['message']     = "Please complete your profile";
            $data['status']      = 200;
            $redirect_to = "/account";
         }
         else{
            $redirect_to ="/";
            $data = [];
         }

         return redirect($redirect_to)->with($data);
 
    }

}
