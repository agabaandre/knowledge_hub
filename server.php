<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$basePath = '';
$envFile = __DIR__.'/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (str_starts_with($line, 'APP_URL=')) {
            $appUrl = trim(substr($line, strlen('APP_URL=')), " \t\"'");
            $basePath = rtrim(parse_url($appUrl, PHP_URL_PATH) ?: '', '/');
            break;
        }
    }
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');

if ($basePath !== '' && ($uri === $basePath || str_starts_with($uri, $basePath.'/'))) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
    $query = ! empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
    $_SERVER['REQUEST_URI'] = $uri.$query;
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
