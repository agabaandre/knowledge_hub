<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Log;

class CustomEnsureAPISwaggerAccessIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        Log::debug("Token::". json_encode($request->all()));

        // Bypass Sanctum Authentication for Swagger UI
        if ($this->isValidAPIUser($request)){
            
           // Apply Sanctum Authentication
       
            $sanctumMiddleware = new EnsureFrontendRequestsAreStateful;
            return $sanctumMiddleware->handle($request, $next);

        }else{

            return response(["error"=>"Unauthorized API access,Accessible to developer accounts only"],401);
        }

    }

    protected function isValidAPIUser($request)
    {
        Log::debug("Request::". json_encode($request));

        $user = current_user();

        if($user){
            $apiUser = ApiClient::where('user_id',$user->id)->first();
            Log::debug("API user::".json_encode($apiUser));
            return (@$apiUser->id !=null)?true:false;
        }
        else{
            Log::debug("NO API user::");
            return false;
        }
    }
}
