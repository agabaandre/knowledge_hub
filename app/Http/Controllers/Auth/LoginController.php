<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Support\PostLoginRedirect;
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

    public function showLoginForm(Request $request)
    {
        PostLoginRedirect::sanitizeSessionIntended();

        $redirect = $this->safeRedirectUrl($request->query('redirect'));
        if ($redirect !== null) {
            session(['url.intended' => $redirect]);
        }

        if (! session()->has('alert')) {
            $reason = (string) $request->query('reason', '');
            if ($reason === 'forum') {
                session()->flash('alert_class', 'info');
                session()->flash('alert', 'Please log in to create a forum discussion.');
            } elseif ($reason === 'publication') {
                session()->flash('alert_class', 'info');
                session()->flash('alert', 'Please log in to publish a resource.');
            }
        }

        return view('auth.login');
    }

    protected function safeRedirectUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return PostLoginRedirect::isSafe($url) ? $url : null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '' && str_starts_with($url, $appUrl.'/')) {
            return PostLoginRedirect::isSafe($url) ? $url : null;
        }

        return null;
    }

    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        PostLoginRedirect::sanitizeSessionIntended();
    }

    protected function sendLoginResponse(\Illuminate\Http\Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : PostLoginRedirect::intended($this->redirectPath());
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

        $messages = [];

        // Check if we're on localhost or local environment
        $isLocalhost = in_array($request->getHost(), ['localhost', '127.0.0.1']) || 
                       app()->environment('local', 'testing');
        
        // Add reCAPTCHA validation if site key is configured AND not on localhost
        $recaptchaSiteKey = config('recaptcha.api_site_key');
        if ($recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost) {
            $rules['g-recaptcha-response'] = 'required';
            $messages['g-recaptcha-response.required'] = 'Please complete the CAPTCHA to proceed.';
        }

        $request->validate($rules, $messages);

        // Validate reCAPTCHA response - only if not on localhost
        if ($recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost) {
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
