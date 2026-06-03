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
        if ($request->is('install', 'install/*')) {
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
}
