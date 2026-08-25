<?php

declare(strict_types=1);

/**
 * Serve published static files (./setup-production.sh) or reverse-proxy to Next.js.
 * Same role as staff-portal/spa-static.php: Apache keeps the public URL
 * (http://localhost/knowledge_hub/front_end) while production files live in public-spa/.
 */
require __DIR__.'/front-end-static.php';

$autoload = dirname(__DIR__).'/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

$applySecurityHeaders = static function (): void {
    if (! class_exists(\App\Support\SecurityHeaders::class)) {
        return;
    }
    \App\Support\SecurityHeaders::apply(static function (string $name, string $value): void {
        header($name.': '.$value, true);
    });
};

khub_front_end_try_send_published($_SERVER['REQUEST_URI'] ?? '/knowledge_hub/front_end');

$nextOrigin = getenv('KHUB_FRONT_END_ORIGIN') ?: 'http://127.0.0.1:3001';
$uri = $_SERVER['REQUEST_URI'] ?? '/knowledge_hub/front_end';
$target = rtrim($nextOrigin, '/').$uri;

if (! function_exists('curl_init')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    $applySecurityHeaders();
    echo "PHP curl is required to proxy the Knowledge Hub frontend.\n";
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$headers = [];
foreach ($_SERVER as $key => $value) {
    if (! str_starts_with($key, 'HTTP_')) {
        continue;
    }
    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
    if (in_array(strtolower($name), ['host', 'connection', 'content-length'], true)) {
        continue;
    }
    $headers[] = $name.': '.$value;
}
if (! empty($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'Content-Type: '.$_SERVER['CONTENT_TYPE'];
}

$ch = curl_init($target);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if (! in_array($method, ['GET', 'HEAD'], true)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input') ?: '');
}

$response = curl_exec($ch);
if ($response === false) {
    $err = curl_error($ch);
    curl_close($ch);
    http_response_code(502);
    header('Content-Type: text/html; charset=utf-8');
    $applySecurityHeaders();
    echo '<!DOCTYPE html><html><head><title>Frontend not running</title></head><body>';
    echo '<h1>Knowledge Hub frontend is not running</h1>';
    echo '<p>Publish a production build (no Node required afterwards):</p>';
    echo '<pre>cd front_end && ./setup-production.sh</pre>';
    echo '<p>Or start the Next.js dev server:</p>';
    echo '<pre>cd front_end && npm install --legacy-peer-deps && npm run dev</pre>';
    echo '<p>Then open <a href="/knowledge_hub/front_end">/knowledge_hub/front_end</a>.</p>';
    echo '<p style="color:#666">('.htmlspecialchars($err, ENT_QUOTES).')</p>';
    echo '</body></html>';
    exit;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 502;
curl_close($ch);

$rawHeaders = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
http_response_code((int) $status);
$skip = [
    'transfer-encoding',
    'connection',
    'keep-alive',
    'x-content-type-options',
    'x-frame-options',
    'content-security-policy',
    'referrer-policy',
    'strict-transport-security',
    'permissions-policy',
];
foreach (explode("\r\n", $rawHeaders) as $line) {
    if (! str_contains($line, ':')) {
        continue;
    }
    [$name, $value] = explode(':', $line, 2);
    if (in_array(strtolower(trim($name)), $skip, true)) {
        continue;
    }
    header(trim($name).': '.trim($value), false);
}
$applySecurityHeaders();
echo $body;
