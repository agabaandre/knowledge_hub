<?php

declare(strict_types=1);

function khub_front_end_request_relpath(string $uri): string
{
    $path = parse_url($uri, PHP_URL_PATH);
    if (! is_string($path) || $path === '') {
        $path = '/';
    }

    $prefix = '/knowledge_hub/front_end';
    if (str_starts_with($path, $prefix)) {
        $path = substr($path, strlen($prefix)) ?: '/';
    }

    if ($path === '' || $path === '/') {
        return '/';
    }

    return '/'.trim($path, '/');
}

function khub_front_end_resolve_file(string $relPath, string $spaDir): ?string
{
    if ($relPath === '' || str_contains($relPath, '..')) {
        return null;
    }

    $rel = ltrim($relPath, '/');
    if ($rel === '') {
        $index = $spaDir.'/index.html';

        return is_file($index) ? $index : null;
    }

    $direct = $spaDir.'/'.$rel;
    if (is_file($direct)) {
        return $direct;
    }

    $index = $direct.'/index.html';
    if (is_file($index)) {
        return $index;
    }

    return null;
}

function khub_front_end_mime(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return match ($ext) {
        'js', 'mjs' => 'application/javascript; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'map', 'json' => 'application/json; charset=utf-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'ico' => 'image/x-icon',
        'html', 'htm' => 'text/html; charset=utf-8',
        'txt' => 'text/plain; charset=utf-8',
        'xml' => 'application/xml; charset=utf-8',
        default => 'application/octet-stream',
    };
}

function khub_front_end_send_file(string $path, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: '.khub_front_end_mime($path));
    header('X-Content-Type-Options: nosniff');
    header('X-Khub-Front-End: static');
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['js', 'mjs', 'css', 'woff', 'woff2', 'ttf'], true)) {
        header('Cache-Control: public, max-age=31536000, immutable');
    } else {
        header('Cache-Control: no-cache, must-revalidate');
    }
    header('Content-Length: '.(string) filesize($path));
    readfile($path);
    exit;
}

function khub_front_end_try_send_published(string $uri): bool
{
    $spa = __DIR__.'/public-spa';
    if (! is_file($spa.'/index.html')) {
        return false;
    }

    $rel = khub_front_end_request_relpath($uri);
    $file = khub_front_end_resolve_file($rel, $spa);
    if (is_string($file)) {
        khub_front_end_send_file($file);
    }

    $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
    $isDocument = $rel === '/' || $ext === '' || in_array($ext, ['html', 'htm'], true);
    if ($isDocument && is_file($spa.'/404.html')) {
        khub_front_end_send_file($spa.'/404.html', 404);
    }

    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Khub-Front-End: static');
    echo "Not found\n";
    exit;
}
