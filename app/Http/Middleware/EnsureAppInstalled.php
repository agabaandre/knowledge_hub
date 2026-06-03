<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;

class EnsureAppInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (app(InstallerService::class)->isInstalled()) {
            return $next($request);
        }

        if ($request->is('install', 'install/*')) {
            return $next($request);
        }

        return redirect()->route('install.index');
    }
}
