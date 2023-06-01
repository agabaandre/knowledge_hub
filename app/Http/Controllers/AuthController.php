<?php

namespace App\Http\Controllers;

use App\Repositories\AuthorsRepository;
use App\Repositories\FaqsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $usersRepo, $authorsRepo;

    public function __construct( UsersRepository $usersRepo, AuthorsRepository $authorsRepo)
    {
        $this->usersRepo       = $usersRepo;
        $this->authorsRepo     = $authorsRepo;
    }

    public function register(Request $request){

        $request->validate([
            'firstname'=> ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

                $message = "Your e-mail is already verified. You can now login.";

            }
        }
  
      return redirect()->route('login')->with('message', $message);
    }

    
    public function update_profile(Request $request){

        $val_rules =[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'preferences'=>'required'
        ];

        $request->validate($val_rules);

        $saved   = $this->usersRepo->update_profile($request);

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

}
