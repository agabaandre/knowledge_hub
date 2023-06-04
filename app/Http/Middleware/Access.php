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
        // $ip = '162.159.24.227'; /* Static IP address */
        // $visitorInfo = Location::get($ip);

        // AccessLogJob::dispatch($visitorInfo);

        return $next($request);
    }
}
