<?php

namespace App\Http\Middleware;

use App\Support\SecurityHeaders;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (SecurityHeaders::all() as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }
}
