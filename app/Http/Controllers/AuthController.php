<?php

namespace App\Http\Controllers;

use App\Repositories\FaqsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    private $usersRepo;

    public function __construct( UsersRepository $usersRepo)
    {
        $this->usersRepo       = $usersRepo;
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
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }
  
      return redirect()->route('login')->with('message', $message);
    }

}
