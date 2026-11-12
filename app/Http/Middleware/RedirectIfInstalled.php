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

        if ($installer->isInstallerDisabled()) {
            abort(404);
        }

        if ($installer->isInstalled()) {
            if ($request->routeIs('install.complete') && $request->session()->pull('install_show_complete')) {
                return $next($request);
            }

            // Installed (including DB-has-data) — do not expose the installer.
            abort(404);
        }

        return $next($request);
    }
}
