<?php

namespace App\Support;

/**
 * Permanent per-hub storage identifier derived from APP_URL (domain + subfolder path).
 *
 * Examples:
 *   https://khub.com/andrew  → khub-com-andrew
 *   https://hub.example.com  → hub-example-com
 *   http://localhost:8080    → localhost-8080
 */
class HubSiteIdentifier
{
    public static function fromAppUrl(?string $appUrl = null): string
    {
        $appUrl = trim((string) ($appUrl ?? env('APP_URL', 'http://localhost')));
        if ($appUrl === '') {
            return 'default';
        }

        $parsed = parse_url($appUrl);
        $host = strtolower((string) ($parsed['host'] ?? 'localhost'));
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        $port = isset($parsed['port']) ? (int) $parsed['port'] : null;
        $path = trim((string) ($parsed['path'] ?? ''), '/');

        $segments = array_filter(explode('.', $host), fn ($part) => $part !== '');
        $slug = implode('-', $segments);

        if ($port && ! in_array($port, [80, 443], true)) {
            $slug .= '-'.$port;
        }

        if ($path !== '') {
            $pathSlug = preg_replace('/[^a-z0-9]+/', '-', strtolower($path));
            $pathSlug = trim((string) $pathSlug, '-');
            if ($pathSlug !== '') {
                $slug .= '-'.$pathSlug;
            }
        }

        return self::sanitize($slug ?: 'default');
    }

    public static function sanitize(string $id): string
    {
        $id = strtolower(trim($id));
        $id = preg_replace('/[^a-z0-9-]+/', '-', $id) ?? '';
        $id = preg_replace('/-+/', '-', $id) ?? '';

        return trim($id, '-') ?: 'default';
    }

    public static function hostDataRoot(?string $siteId = null): string
    {
        $siteId = $siteId ?? self::fromAppUrl();
        $base = config('hub_storage.host_data_root', '/var/khubdata');

        if (PHP_OS_FAMILY === 'Windows') {
            $base = config('hub_storage.host_data_root_windows', 'C:\\khubdata');
        }

        return rtrim($base, '/\\').DIRECTORY_SEPARATOR.$siteId;
    }

    /**
     * @return array{files: string, sql_backups: string, site_root: string}
     */
    public static function defaultPaths(?string $siteId = null): array
    {
        $siteRoot = self::hostDataRoot($siteId);

        return [
            'site_root' => $siteRoot,
            'files' => $siteRoot.DIRECTORY_SEPARATOR.'files',
            'sql_backups' => $siteRoot.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.'sql',
        ];
    }
}
