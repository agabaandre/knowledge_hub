<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationPrerequisites
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isInstallRequest($request)) {
            return $next($request);
        }

        $installer = app(InstallerService::class);
        if (! $installer->isInstalled()) {
            return $next($request);
        }

        $result = $installer->applicationPrerequisites();
        if ($result['ok']) {
            return $next($request);
        }

        return response()->view('errors.prerequisites', [
            'checks' => $result['checks'],
        ], 503);
    }

    protected function isInstallRequest(Request $request): bool
    {
        if ($request->routeIs('install.*')) {
            return true;
        }

        $path = trim($request->path(), '/');

        return $path === 'install'
            || str_starts_with($path, 'install/')
            || str_ends_with($path, '/install')
            || str_contains($path, '/install/');
    }
}
