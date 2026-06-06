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

        if ($installer->isInstalled()) {
            if ($request->routeIs('install.complete') && $request->session()->pull('install_show_complete')) {
                return $next($request);
            }

            if ($request->routeIs('install.*')) {
                abort(403, 'The installer has been disabled. This application is already installed.');
            }

            return redirect('/');
        }

        if ($installer->isExistingDeployment() && $request->routeIs('install.*')) {
            abort(403, 'This application appears to be already deployed: Composer dependencies are present and the database contains data. Run `php artisan khub:mark-installed` on the server, or set APP_INSTALLED=true in .env, then visit the site home page.');
        }

        return $next($request);
    }
}
