<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class AuthenticateWithPassportToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->headers->all();

        \Log::info('Request Headers:'.json_encode($headers));
        updateUSerPushToken($request);

        
        // Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if ($token) {
            
            $decodedPayload = $this->decodeJWT($token);

            // Get the user ID from the 'sub' field in the payload
            $userId = $decodedPayload['sub'];
            $user = User::find($userId);
            auth()->setUser($user);
            // Attach the user to the request
            $request->merge(['user' => $user]);
        }

        return $next($request);
    }

    private function base64UrlDecode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    private function decodeJWT($jwt) {
        // Split the JWT into its three parts
        list($header, $payload, $signature) = explode('.', $jwt);
        
        // Decode the payload (which is the second part)
        $decodedPayload = $this->base64UrlDecode($payload);
        
        // Convert the decoded payload into an associative array
        return json_decode($decodedPayload, true);
    }
}

