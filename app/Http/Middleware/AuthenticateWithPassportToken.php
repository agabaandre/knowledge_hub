<?
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Token;

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
        // Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if ($token) {
            // Find the token in the Passport tokens table
            $passportToken = Token::where('id', hash('sha256', $token))->first();

            if ($passportToken && !$passportToken->revoked) {
                // Get the associated user from the token
                $user = $passportToken->user;

                // Attach the user to the request for later use
                $request->merge(['user' => $user]);

                // Optionally: Store the user in the session
                session(['user' => $user]);
            }
        }

        return $next($request);
    }
}
