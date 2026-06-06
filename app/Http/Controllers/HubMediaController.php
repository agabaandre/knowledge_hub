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
        $absolute = $storage->absolutePath($path);

        if (! is_file($absolute)) {
            abort(404);
        }

        $root = realpath($storage->filesRoot());
        $real = realpath($absolute);
        if (! $root || ! $real || ! str_starts_with($real, $root)) {
            abort(403);
        }

        return new BinaryFileResponse($real);
    }
}
