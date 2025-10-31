<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. You're free to explore this trait
    | and override any methods you wish to tweak.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request),
            function ($user, $token) use ($request) {
                // Use Exchange OAuth instead of Laravel Mail facade
                $this->sendResetLinkViaExchange($user, $token);
            }
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Send password reset link via Exchange OAuth
     *
     * @param  \App\Models\User  $user
     * @param  string  $token
     * @return void
     */
    protected function sendResetLinkViaExchange($user, $token)
    {
        try {
            $resetUrl = url('password/reset?token=' . $token);
            
            $mailData = (object) [
                'email' => $user->email,
                'subject' => 'Reset Your Password - Africa CDC Knowledge Hub',
                'body' => view('emails.password_reset', [
                    'name' => $user->name,
                    'token' => $token,
                    'resetUrl' => $resetUrl,
                ])->render()
            ];
            
            $result = send_email($mailData);
            
            if (!$result || (is_array($result) && !($result['success'] ?? false))) {
                \Log::error('Failed to send password reset email via Exchange', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => is_array($result) ? ($result['message'] ?? 'Unknown error') : 'Unknown error'
                ]);
            } else {
                \Log::info('Password reset email sent via Exchange', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Exception sending password reset email via Exchange: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => get_class($e)
            ]);
        }
    }
}
