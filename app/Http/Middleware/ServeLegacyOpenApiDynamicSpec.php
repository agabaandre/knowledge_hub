<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DocsController;
use Closure;
use Illuminate\Http\Request;

/**
 * Serves the dynamic OpenAPI JSON before routing.
 *
 * Swagger UI at `/docs` fetches `docs/openapi-dynamic.json`. That path must not
 * live under l5-swagger's `docs/spec/*` routes (which shadow custom web routes when
 * route cache or server rewrite order differs). This middleware also keeps the
 * legacy alias `docs/spec/api-docs.dynamic.json` working.
 */
class ServeLegacyOpenApiDynamicSpec
{
    /** @var array<int, string> */
    private const DYNAMIC_SPEC_PATHS = [
        'docs/openapi-dynamic.json',
        'docs/spec/api-docs.dynamic.json',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        if (in_array($path, self::DYNAMIC_SPEC_PATHS, true)) {
            return app(DocsController::class)->openApiJson($request);
        }

        return $next($request);
    }
}
