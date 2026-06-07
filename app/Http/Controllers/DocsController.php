<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DocsController extends Controller
{
    /**
     * Serve OpenAPI JSON with dynamic `servers` from env/current host.
     *
     * This avoids hardcoding server URLs in the generated swagger file.
     */
    public function openApiJson(Request $request)
    {
        $path = storage_path('api-docs/api-docs.json');
        if (! File::exists($path)) {
            try {
                Artisan::call('l5-swagger:generate');
            } catch (\Throwable) {
                // Fall through to 404 below.
            }
        }

        if (! File::exists($path)) {
            return response()->json([
                'message' => 'OpenAPI spec not found. Run: php artisan l5-swagger:generate',
            ], 404);
        }

        $raw = File::get($path);
        $spec = json_decode($raw, true);
        if (!is_array($spec)) {
            return response()->json(['message' => 'OpenAPI spec is invalid JSON.'], 500);
        }

        $serverUrl = env('L5_OPENAPI_SERVER_URL');
        if (!$serverUrl) {
            $serverUrl = rtrim((string) config('app.url', $request->getSchemeAndHttpHost()), '/');
        }

        $spec['servers'] = [
            [
                'url' => rtrim((string) $serverUrl, '/'),
                'description' => 'Dynamic server from env/current host.',
            ],
        ];

        return response()->json($spec);
    }
}

