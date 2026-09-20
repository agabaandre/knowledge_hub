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

        $headers = $this->isApiRequest($request) ? SecurityHeaders::api() : SecurityHeaders::web();

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->is('api') || $request->is('api/*');
    }
}
