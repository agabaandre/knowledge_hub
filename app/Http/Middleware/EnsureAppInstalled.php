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

        if ($this->isInstallRequest($request)) {
            return $next($request);
        }

        return redirect()->route('install.index');
    }

    protected function isInstallRequest(Request $request): bool
    {
        if ($request->routeIs('install.*')) {
            return true;
        }

        // Subdirectory hubs (e.g. /ghana/install) may report path as "ghana/install".
        $path = trim($request->path(), '/');

        return $path === 'install'
            || str_starts_with($path, 'install/')
            || str_ends_with($path, '/install')
            || str_contains($path, '/install/');
    }
}
