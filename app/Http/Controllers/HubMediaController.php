<?php

namespace App\Http\Controllers;

use App\Services\HubStorageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HubMediaController extends Controller
{
    public function show(Request $request, string $path, HubStorageService $storage)
    {
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $resolved = $storage->resolveReadableFile($path);

        if ($resolved === null) {
            abort(404);
        }

        return new BinaryFileResponse($resolved['path']);
    }
}
