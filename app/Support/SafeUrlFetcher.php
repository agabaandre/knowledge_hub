<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Single choke point for fetching user-supplied URLs. Blocks SSRF:
 * http/https only, DNS-resolved targets must be public addresses
 * (no loopback, RFC1918, link-local, CGNAT, metadata or ULA ranges),
 * redirects are re-validated hop by hop, and the resolved IP is pinned
 * on the request so DNS rebinding cannot swap the target mid-flight.
 */
final class SafeUrlFetcher
{
    public const MAX_REDIRECTS = 5;

    public const MAX_BYTES = 52428800; // 50 MB

    public const TIMEOUT_SECONDS = 30;

    /**
     * Validate shape + destination. Returns the trimmed URL or null.
     */
    public static function validate(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || strlen($url) > 2048 || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }
        if (isset($parts['user']) || isset($parts['pass'])) {
            return null; // credentials in URLs are always suspicious
        }

        return self::resolvePublicIp($host) === null ? null : $url;
    }

    /**
     * Resolve a host and return one public IP, or null when any resolved
     * address is private/reserved (one bad A record poisons the lot).
     */
    public static function resolvePublicIp(string $host): ?string
    {
        $ips = self::resolveAll($host);
        if ($ips === []) {
            return null;
        }
        foreach ($ips as $ip) {
            if (self::isForbiddenIp($ip)) {
                return null;
            }
        }

        return $ips[0];
    }

    /** @return list<string> */
    private static function resolveAll(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $ips = [];
        foreach ((array) @dns_get_record($host, DNS_A) as $record) {
            if (! empty($record['ip'])) {
                $ips[] = (string) $record['ip'];
            }
        }
        foreach ((array) @dns_get_record($host, DNS_AAAA) as $record) {
            if (! empty($record['ipv6'])) {
                $ips[] = (string) $record['ipv6'];
            }
        }

        return array_values(array_unique($ips));
    }

    public static function isForbiddenIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return true;
        }

        // Public per PHP's reserved/private flags?
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        // Ranges the flags miss: link-local & metadata, CGNAT, IPv6 ULA / link-local / v4-mapped.
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            $long = ip2long($ip);
            $cidrs = [
                ['169.254.0.0', 16], // link-local + cloud metadata (169.254.169.254)
                ['100.64.0.0', 10],  // CGNAT
                ['192.0.0.0', 24],
                ['198.18.0.0', 15],
            ];
            foreach ($cidrs as [$net, $bits]) {
                if (($long >> (32 - $bits)) === (ip2long($net) >> (32 - $bits))) {
                    return true;
                }
            }

            return false;
        }

        $packed = @inet_pton($ip);
        if ($packed === false || strlen($packed) !== 16) {
            return true;
        }
        $first = ord($packed[0]);
        if (($first & 0xFE) === 0xFC) { // fc00::/7 ULA
            return true;
        }
        if ($first === 0xFE && (ord($packed[1]) & 0xC0) === 0x80) { // fe80::/10 link-local
            return true;
        }
        if (substr($packed, 0, 12) === "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
            return self::isForbiddenIp(inet_ntop(substr($packed, 12)) ?: ''); // v4-mapped
        }

        return false;
    }

    /**
     * Fetch with per-hop validation and IP pinning.
     *
     * @return array{status: int, body: string, content_type: string}|null
     */
    public static function fetch(string $url, int $maxBytes = self::MAX_BYTES, int $timeout = self::TIMEOUT_SECONDS): ?array
    {
        for ($hop = 0; $hop <= self::MAX_REDIRECTS; $hop++) {
            $url = self::validate($url);
            if ($url === null) {
                return null;
            }

            $parts = parse_url($url);
            $host = strtolower((string) $parts['host']);
            $port = (int) ($parts['port'] ?? (strtolower((string) $parts['scheme']) === 'https' ? 443 : 80));
            $pinnedIp = self::resolvePublicIp($host);
            if ($pinnedIp === null) {
                return null;
            }

            $result = self::fetchOnce($url, $host, $port, $pinnedIp, $maxBytes, $timeout);
            if ($result === null) {
                return null;
            }

            if ($result['status'] >= 300 && $result['status'] < 400 && $result['location'] !== '') {
                $url = self::absolutizeLocation($url, $result['location']);
                continue;
            }

            return ['status' => $result['status'], 'body' => $result['body'], 'content_type' => $result['content_type']];
        }

        Log::warning('SafeUrlFetcher: redirect limit exceeded', ['url' => substr($url, 0, 200)]);

        return null;
    }

    /** @return array{status: int, body: string, content_type: string, location: string}|null */
    private static function fetchOnce(string $url, string $host, int $port, string $pinnedIp, int $maxBytes, int $timeout): ?array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }

        $resolveIp = str_contains($pinnedIp, ':') ? "[{$pinnedIp}]" : $pinnedIp;
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_MAXFILESIZE => $maxBytes,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RESOLVE => ["{$host}:{$port}:{$resolveIp}"],
            CURLOPT_USERAGENT => 'KnowledgeHub-SafeFetcher/1.0',
            CURLOPT_NOPROGRESS => false,
            CURLOPT_PROGRESSFUNCTION => static function ($ch, $dlTotal, $dlNow) use ($maxBytes) {
                return ($dlNow > $maxBytes || ($dlTotal > 0 && $dlTotal > $maxBytes)) ? 1 : 0;
            },
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $contentType = (string) (curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '');
        $location = (string) (curl_getinfo($ch, CURLINFO_REDIRECT_URL) ?: '');
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($body === false || $errno !== 0) {
            Log::info('SafeUrlFetcher: fetch failed', ['url' => substr($url, 0, 200), 'errno' => $errno]);

            return null;
        }

        return ['status' => $status, 'body' => (string) $body, 'content_type' => $contentType, 'location' => $location];
    }

    private static function absolutizeLocation(string $base, string $location): string
    {
        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($base);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '').(isset($parts['port']) ? ':'.$parts['port'] : '');
        if (str_starts_with($location, '/')) {
            return $origin.$location;
        }

        $dir = rtrim(dirname($parts['path'] ?? '/'), '/');

        return $origin.$dir.'/'.$location;
    }
}
