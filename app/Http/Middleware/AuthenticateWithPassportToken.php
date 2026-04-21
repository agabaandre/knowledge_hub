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
        if (config('app.debug')) {
            \Log::debug('AuthenticateWithPassportToken: incoming request', [
                'path' => $request->path(),
            ]);
        }
        updateUSerPushToken($request);

        // Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if (!empty($token) && !auth()->user()) {
            // Only Laravel Passport personal access tokens are JWT-shaped (header.payload.signature).
            // Google / other OAuth access tokens (e.g. ya29...) are opaque — do not parse as JWT.
            $decodedPayload = $this->decodeJWT($token);
            if (!is_array($decodedPayload) || !isset($decodedPayload['sub'], $decodedPayload['jti'])) {
                return $next($request);
            }

            $userId = $decodedPayload['sub'];
            $tokenId = $decodedPayload['jti'];
            if (config('app.debug')) {
                \Log::debug('AuthenticateWithPassportToken: Passport JWT', ['jti' => $tokenId]);
            }
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
    
    /**
     * Decode a JWT payload only when the string is a valid 3-segment JWT.
     *
     * @return array<string, mixed>|null
     */
    private function decodeJWT(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        [$header, $payload, $signature] = $parts;
        if ($header === '' || $payload === '' || $signature === '') {
            return null;
        }

        $decodedPayload = $this->base64UrlDecode($payload);
        if ($decodedPayload === false || $decodedPayload === '') {
            return null;
        }

        $data = json_decode($decodedPayload, true);

        return is_array($data) ? $data : null;
    }

}
