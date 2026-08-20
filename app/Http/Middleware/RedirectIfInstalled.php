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

        // Allow the web installer even when vendor/DB already exist (common after
        // bare-metal file copy or a partial provision). Operators can finish setup
        // or run `php artisan khub:mark-installed` when the app is fully ready.
        if ($installer->isExistingDeployment() && $request->routeIs('install.index')) {
            $request->session()->now(
                'install_existing_deployment_notice',
                'Composer dependencies and database tables were detected. Continue only if you intend to finish setup on this instance. If the site is already live, run `php artisan khub:mark-installed` instead.'
            );
        }

        return $next($request);
    }
}
