<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Biscolab\ReCaptcha\Facades\ReCaptcha;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

        $messages = [];

        // Add reCAPTCHA validation if site key is configured
        $recaptchaSiteKey = config('recaptcha.api_site_key');
        if ($recaptchaSiteKey && !empty($recaptchaSiteKey)) {
            $rules['g-recaptcha-response'] = 'required';
            $messages['g-recaptcha-response.required'] = 'Please complete the CAPTCHA to proceed.';
        }

        $request->validate($rules, $messages);

        // Validate reCAPTCHA response - must be provided and valid if site key is configured
        if ($recaptchaSiteKey && !empty($recaptchaSiteKey)) {
            // Check if reCAPTCHA response is provided
            if (!$request->filled('g-recaptcha-response')) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Please complete the CAPTCHA to proceed.',
                ]);
            }
            
            // Validate the reCAPTCHA response
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!ReCaptcha::validate($recaptchaResponse)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.',
                ]);
            }
        }
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        // Only allow active & verified accounts
        $credentials['is_verified'] = 1;
        $credentials['status'] = 1; // Active

        return Auth::attempt($credentials, $request->filled('remember'));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $credentials = $this->credentials($request);

        // Check if the user exists and is not verified
        $user = User::where('email', $credentials['email'])->first();
        if ($user && !$user->is_verified) {
            throw ValidationException::withMessages([
                'email' => 'User is not verified',
            ]);
        }

        // Check if user is not active
        if ($user && isset($user->status) && intval($user->status) !== 1) {
            throw ValidationException::withMessages([
                'email' => 'User account is not active',
            ]);
        }

        // Default failed login response
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
