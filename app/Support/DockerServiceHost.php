<?php

namespace App\Support;

class DockerServiceHost
{
    public static function insideContainer(): bool
    {
        return is_file('/.dockerenv');
    }

    /**
     * Docker Compose service names (mysql, redis) do not resolve on the host.
     * When Artisan runs on the Mac/Windows host, map them to localhost.
     */
    public static function resolve(string $host, string $fallback = '127.0.0.1'): string
    {
        if ($host === '' || self::insideContainer() || self::isLoopback($host)) {
            return $host;
        }

        if (self::hostnameResolves($host)) {
            return $host;
        }

        return $fallback;
    }

    /**
     * @param  mixed  $port
     * @param  mixed  $publishedPort
     * @return mixed
     */
    public static function resolvePort(string $host, $port, $publishedPort = '3307')
    {
        if (self::insideContainer() || self::isLoopback($host) || self::hostnameResolves($host)) {
            return $port;
        }

        if (in_array($host, ['mysql', 'mariadb', 'db'], true)) {
            return $publishedPort;
        }

        return $port;
    }

    public static function hostnameResolves(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        $resolved = @gethostbyname($host);

        return is_string($resolved) && $resolved !== '' && $resolved !== $host;
    }

    private static function isLoopback(string $host): bool
    {
        return in_array($host, ['127.0.0.1', 'localhost', '::1'], true);
    }
}
