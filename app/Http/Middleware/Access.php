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
        //  /* Static IP address */
        // $visitorInfo = Location::get($ip);

        $ip = env('APP_DEBUG')?'41.210.143.17':$request->ip();
     
        $logData = $request->all();

        unset($logData['cover']);
        unset($logData['files']);
        unset($logData['image']);
        unset($logData['photo']);
        unset($logData['logo']);
        unset($logData['favicon']);

         AccessLogJob::dispatch($ip,$logData);

        return $next($request);
    }
}
