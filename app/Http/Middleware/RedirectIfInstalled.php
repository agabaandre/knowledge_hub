<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installer = app(InstallerService::class);

        if (! $installer->isInstalled()) {
            return $next($request);
        }

        if ($request->routeIs('install.complete') && $request->session()->pull('install_show_complete')) {
            return $next($request);
        }

        if ($request->routeIs('install.*')) {
            abort(403, 'The installer has been disabled. This application is already installed.');
        }

        return redirect('/');
    }
}
