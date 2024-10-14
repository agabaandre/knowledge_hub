<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;

class AuthenticateWithPassportToken
{
    protected $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

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

        if ($token && !auth()->user()) {

            $decodedPayload = $this->decodeJWT($token);
            $userId = $decodedPayload['sub'];

            $tokenId = $decodedPayload['jti']; // Assuming 'jti' is the token ID
            \Log::info('Token ID: '.$tokenId);
            $tokenRecord = $this->tokenRepository->find($tokenId);

            if ($tokenRecord && !$tokenRecord->revoked && $tokenRecord->user_id == $userId) {
                auth()->setUser($tokenRecord->user);
                $request->merge(['user' => $tokenRecord->user]);
            }
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

    private function isTokenValid(User $user, $token)
    {
        // Check if the token exists and is valid for the user
        return Token::where('id', $token)
                    ->where('user_id', $user->id)
                    ->where('revoked', false)
                    ->exists();
    }

    private function getUserFromToken($token)
    {
        $decodedPayload = $this->decodeJWT($token);
        $userId = $decodedPayload['sub'];
        return User::find($userId);
    }
}
