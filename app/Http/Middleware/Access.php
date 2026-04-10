<?php

namespace App\Http\Middleware;

use App\Jobs\AccessLogJob;
use Closure;
use Illuminate\Http\Request;
//use Stevebauman\Location\Facades\Location;

class Access
{

    public function handle(Request $request, Closure $next)
    {
        $ip = env('APP_DEBUG')?'127:0:0:1':$request->ip();
        
        // Create a unique session key based on IP and user ID (if authenticated)
        $userId = auth()->id();
        $sessionKey = 'access_logged_' . md5($ip . '_' . ($userId ?? 'guest'));
        
        // Only log once per session for this IP/user combination
        if (!session()->has($sessionKey)) {
            $logData = $request->all();

            unset($logData['cover']);
            unset($logData['files']);
            unset($logData['attachments']);
            unset($logData['file']);
            unset($logData['image']);
            unset($logData['photo']);
            unset($logData['logo']);
            unset($logData['favicon']);
            unset($logData['spotlight_banner']);
            unset($logData['flag']);

            AccessLogJob::dispatch($ip, $logData, $userId);
            
            // Mark as logged in this session (session lasts until browser closes or expires)
            session()->put($sessionKey, true);
        }
       
        return $next($request);
    }
}
