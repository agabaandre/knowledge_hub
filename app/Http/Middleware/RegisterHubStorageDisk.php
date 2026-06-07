<?php

namespace App\Http\Middleware;

use App\Services\HubStorageService;
use Closure;
use Illuminate\Http\Request;

class RegisterHubStorageDisk
{
    public function handle(Request $request, Closure $next)
    {
        app(HubStorageService::class)->registerDiskConfig();

        return $next($request);
    }
}
