<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DocsController;
use Closure;
use Illuminate\Http\Request;

/**
 * Serves the dynamic OpenAPI JSON for the legacy Swagger UI URL
 * `/docs/spec/api-docs.dynamic.json`.
 *
 * That path sits under l5-swagger's `docs/spec/*` handling and can 404 or be
 * shadowed; this middleware runs before routing and fixes cached `/docs` pages.
 */
class ServeLegacyOpenApiDynamicSpec
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        if ($path === 'docs/spec/api-docs.dynamic.json') {
            return app(DocsController::class)->openApiJson($request);
        }

        return $next($request);
    }
}
