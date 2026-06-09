<?php

namespace App\Support;

class PostLoginRedirect
{
    /**
     * Paths that must never be used as a post-login landing page (JSON/AJAX endpoints).
     *
     * @var list<string>
     */
    private const BLOCKED_PATHS = [
        '/admin/storage-management/migration-status',
        '/admin/storage-management/system-metrics',
        '/admin/storage-management/browse',
        '/admin/storage-management/browse-backups',
        '/admin/storage-management/backup-tables',
        '/admin/storage-management/publication-references',
    ];

    public static function isSafe(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return false;
        }

        $path = self::normalizePath($url);
        if ($path === null) {
            return false;
        }

        foreach (self::BLOCKED_PATHS as $blocked) {
            if ($path === $blocked || str_starts_with($path, $blocked.'/')) {
                return false;
            }
        }

        return true;
    }

    public static function sanitizeSessionIntended(): void
    {
        $intended = session()->get('url.intended');
        if ($intended && ! self::isSafe($intended)) {
            session()->forget('url.intended');
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function intended(string $default = '/')
    {
        self::sanitizeSessionIntended();

        return redirect()->intended($default);
    }

    private static function normalizePath(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            $path = parse_url($url, PHP_URL_PATH);

            return is_string($path) && $path !== '' ? $path : $url;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '' && str_starts_with($url, $appUrl)) {
            $path = parse_url($url, PHP_URL_PATH);

            return is_string($path) && $path !== '' ? $path : null;
        }

        return null;
    }
}
