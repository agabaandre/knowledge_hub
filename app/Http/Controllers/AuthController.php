<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\AuthorsRepository;
use App\Repositories\UsersRepository;
use App\Services\SocialLoginService;
use App\Support\OAuthAccountSecurity;
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
            'country_id' => ['required'],
            'preferences' => ['required', 'array', 'min:1'],
            'job' => ['exclude_if:job_missing,1', 'required_without:job_title_custom', 'nullable', 'string', 'max:255'],
            'job_title_custom' => ['nullable', 'string', 'max:255'],
           // 'g-recaptcha-response'=>'required' //|captcha
        ],[
          //  'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
            //'g-recaptcha-response.captcha' => 'Captcha verification failed, please try again.',
        ]);

        $saved = $this->usersRepo->save($request);
        
        $message = ($saved)?'Registration successful! Your account has been activated. You can now login.':'Request failed try again';
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
            'country_id' => 'required',
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

    public function microsoftLogin(Request $request)
    {
        if ($denied = OAuthAccountSecurity::redirectIfOAuthDenied($request, 'microsoft')) {
            return $denied;
        }

        try {
            $socialUser = Socialite::driver('microsoft')->user();

            $email = OAuthAccountSecurity::normalizedProviderEmail($socialUser->getEmail());
            if (! $email) {
                \Log::warning('Microsoft Login: missing or invalid email from token response');

                return redirect('/login')
                    ->with('alert_class', 'danger')
                    ->with('alert', 'Unable to retrieve a valid email from Microsoft. Please try again.');
            }

            $user_exists = User::where('email', $email)->first();

            if ($user_exists) {
                if ($deny = OAuthAccountSecurity::oauthLoginDeniedMessage($user_exists, OAuthAccountSecurity::canonicalOAuthProvider('microsoft'))) {
                    \Log::warning('OAuth login blocked: provider or auth method mismatch', [
                        'email' => $email,
                        'attempted_provider' => 'microsoft',
                        'stored_provider' => $user_exists->social_provider,
                        'is_social_login' => $user_exists->is_social_login,
                    ]);

                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', $deny);
                }
            }

            // Check if the user already exists in the database
            // If not, create a new user using the social login service
            // The service will auto-create and assign to Africa CDC Staff community

            if ($user_exists) :
                $user = $user_exists;
                if ($user->is_social_login && empty($user->social_provider)) {
                    $user->social_provider = OAuthAccountSecurity::canonicalOAuthProvider('microsoft');
                }
                // Auto-activate and verify existing users on SSO login
                if (!$user->email_verified_at) {
                    $user->email_verified_at = \Carbon\Carbon::now();
                }
                if (!$user->is_verified) {
                    $user->is_verified = 1;
                }
                if ($user->status != 1) {
                    $user->status = 1;
                }
                
                // Update photo from Microsoft if available and user doesn't have one
                try {
                    $microsoftPhoto = null;
                    if (method_exists($socialUser, 'getAvatar')) {
                        $microsoftPhoto = $socialUser->getAvatar();
                    }
                    if (!$microsoftPhoto) {
                        $rawUser = method_exists($socialUser, 'getRaw') ? $socialUser->getRaw() : null;
                        if ($rawUser) {
                            if (is_array($rawUser)) {
                                $microsoftPhoto = $rawUser['photo'] ?? $rawUser['picture'] ?? null;
                            } elseif (is_object($rawUser)) {
                                $microsoftPhoto = $rawUser->photo ?? $rawUser->picture ?? null;
                            }
                        }
                    }
                    
                    // Update photo if Microsoft has one and user doesn't have an external photo
                    $safePhoto = OAuthAccountSecurity::sanitizeStoredAvatarUrl($microsoftPhoto);
                    if ($safePhoto && (empty($user->photo) || ! $user->is_photo_external)) {
                        $user->photo = $safePhoto;
                        $user->is_photo_external = 1;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to update Microsoft photo for existing user', ['error' => $e->getMessage()]);
                }

                $user->save();
            else:
                // Auto-create new user and assign to community
                $user = $this->socialLoginService->microsoftCallback($socialUser);
                
                if (!$user || !$user->id) {
                    \Log::error("Microsoft Login Error: Failed to create user", [
                        'email' => $email,
                        'name' => $socialUser->getName()
                    ]);
                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', 'Failed to create your account. Please try again or contact support.');
                }
                
                \Log::info("Microsoft Login: New user created and auto-assigned to community", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            endif;

            Auth::login($user, false);
            $request->session()->regenerate();

            if (! $user->country_id) {
                $data['alert_class'] = 'success';
                $data['message']     = "Please complete your profile";
                $data['status']      = 200;
                $redirect_to = "/account";
            } else {
                $redirect_to = "/";
                $data = [];
            }

            return redirect($redirect_to)->with($data);
        } catch (\Exception $e) {
            \Log::error('Microsoft Login Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('/login')
                ->with('alert_class', 'danger')
                ->with('alert', 'Microsoft sign-in could not be completed. Please try again.');
        }
    }


    public function googleLogin(Request $request)
    {
        if ($denied = OAuthAccountSecurity::redirectIfOAuthDenied($request, 'google')) {
            return $denied;
        }

        if (! $request->filled('code')) {
            \Log::warning('Google Login: missing authorization code');

            return redirect('/login')
                ->with('alert_class', 'danger')
                ->with('alert', 'Google sign-in could not be completed. Please try again.');
        }

        try {
            $socialUser = Socialite::driver('google')->user();

            $email = OAuthAccountSecurity::normalizedProviderEmail($socialUser->getEmail());
            if (! $email) {
                \Log::warning('Google Login: invalid or missing email from token response');

                return redirect('/login')
                    ->with('alert_class', 'danger')
                    ->with('alert', 'Unable to retrieve a valid email from Google. Please try again.');
            }

            $user_exists = User::where('email', $email)->first();

            if ($user_exists) {
                if ($deny = OAuthAccountSecurity::oauthLoginDeniedMessage($user_exists, OAuthAccountSecurity::canonicalOAuthProvider('google'))) {
                    \Log::warning('OAuth login blocked: provider or auth method mismatch', [
                        'email' => $email,
                        'attempted_provider' => 'google',
                        'stored_provider' => $user_exists->social_provider,
                        'is_social_login' => $user_exists->is_social_login,
                    ]);

                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', $deny);
                }
            }

            if ($user_exists) {
                $user = $user_exists;
                if ($user->is_social_login && empty($user->social_provider)) {
                    $user->social_provider = OAuthAccountSecurity::canonicalOAuthProvider('google');
                }
                if (! $user->email_verified_at) {
                    $user->email_verified_at = \Carbon\Carbon::now();
                }
                if (! $user->is_verified) {
                    $user->is_verified = 1;
                }
                if ($user->status != 1) {
                    $user->status = 1;
                }
                $user->save();
            } else {
                $user = $this->socialLoginService->googleCallback($socialUser);
                if (! $user || ! $user->id) {
                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', 'Failed to create your account. Please try again or contact support.');
                }
            }

            Auth::login($user, false);
            $request->session()->regenerate();

            if (! $user->country_id) {
                $data['alert_class'] = 'success';
                $data['message'] = 'Please complete your profile';
                $data['status'] = 200;
                $redirect_to = '/account';
            } else {
                $redirect_to = '/';
                $data = [];
            }

            return redirect($redirect_to)->with($data);
        } catch (\Exception $e) {
            \Log::error('Google Login Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('/login')
                ->with('alert_class', 'danger')
                ->with('alert', 'Google sign-in could not be completed. Please try again.');
        }
    }

    public function linkedinLogin(Request $request)
    {
        if ($denied = OAuthAccountSecurity::redirectIfOAuthDenied($request, 'linkedin')) {
            return $denied;
        }

        try {
            if (! $request->filled('code')) {
                \Log::warning('LinkedIn Login: no authorization code');

                return redirect('/login')
                    ->with('alert_class', 'danger')
                    ->with('alert', 'LinkedIn authorization failed. Please try again.');
            }

            $socialUser = Socialite::driver('linkedin-openid')->user();

            $email = OAuthAccountSecurity::normalizedProviderEmail($socialUser->getEmail());
            if (! $email) {
                \Log::warning('LinkedIn Login: invalid or missing email from token response');

                return redirect('/login')
                    ->with('alert_class', 'danger')
                    ->with('alert', 'Unable to retrieve a valid email from LinkedIn. Please ensure you grant email permission.');
            }

            $user_exists = User::where('email', $email)->first();

            if ($user_exists) {
                if ($deny = OAuthAccountSecurity::oauthLoginDeniedMessage($user_exists, OAuthAccountSecurity::canonicalOAuthProvider('linkedin'))) {
                    \Log::warning('OAuth login blocked: provider or auth method mismatch', [
                        'email' => $email,
                        'attempted_provider' => 'linkedin',
                        'stored_provider' => $user_exists->social_provider,
                        'is_social_login' => $user_exists->is_social_login,
                    ]);

                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', $deny);
                }
            }

            if ($user_exists) :
                $user = $user_exists;
                if ($user->is_social_login && empty($user->social_provider)) {
                    $user->social_provider = OAuthAccountSecurity::canonicalOAuthProvider('linkedin');
                }
                // Auto-activate and verify existing users on SSO login
                if (!$user->email_verified_at) {
                    $user->email_verified_at = \Carbon\Carbon::now();
                }
                if (!$user->is_verified) {
                    $user->is_verified = 1;
                }
                if ($user->status != 1) {
                    $user->status = 1;
                }
                
                // Update photo from LinkedIn if available and user doesn't have one
                try {
                    $linkedinPhoto = null;
                    if (method_exists($socialUser, 'getAvatar')) {
                        $linkedinPhoto = $socialUser->getAvatar();
                    }
                    if (! $linkedinPhoto) {
                        $rawUser = method_exists($socialUser, 'getRaw') ? $socialUser->getRaw() : null;
                        if ($rawUser) {
                            if (is_array($rawUser)) {
                                $linkedinPhoto = $rawUser['profilePicture'] ?? $rawUser['profile_picture'] ?? null;
                            } elseif (is_object($rawUser)) {
                                $linkedinPhoto = $rawUser->profilePicture ?? $rawUser->profile_picture ?? null;
                            }
                        }
                    }

                    $linkedinPhoto = OAuthAccountSecurity::sanitizeStoredAvatarUrl($linkedinPhoto);

                    // Update photo if LinkedIn has one and user doesn't have an external photo
                    if ($linkedinPhoto && (empty($user->photo) || ! $user->is_photo_external)) {
                        $user->photo = $linkedinPhoto;
                        $user->is_photo_external = 1;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to update LinkedIn photo for existing user', ['error' => $e->getMessage()]);
                }
                
                $user->save();
                \Log::info("LinkedIn Login: Existing user found and activated", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            else:
                // Auto-create new user
                $user = $this->socialLoginService->linkedinCallback($socialUser);
                
                if (!$user || !$user->id) {
                    \Log::error("LinkedIn Login Error: Failed to create user", [
                        'email' => $email,
                        'name' => $socialUser->getName()
                    ]);
                    return redirect('/login')
                        ->with('alert_class', 'danger')
                        ->with('alert', 'Failed to create your account. Please try again or contact support.');
                }
                
                \Log::info("LinkedIn Login: New user created", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            endif;

            Auth::login($user, false);
            $request->session()->regenerate();

            if (! $user->country_id) {
                $data['alert_class'] = 'success';
                $data['message'] = 'Please complete your profile';
                $data['status'] = 200;
                $redirect_to = '/account';
            } else {
                $redirect_to = '/';
                $data = [];
            }

            return redirect($redirect_to)->with($data);
        } catch (\Exception $e) {
            \Log::error('LinkedIn Login Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('/login')
                ->with('alert_class', 'danger')
                ->with('alert', 'LinkedIn sign-in could not be completed. Please try again.');
        }
    }

}
