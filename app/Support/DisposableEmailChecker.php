<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DisposableEmailChecker
{
    public const CACHE_KEY = 'disposable_email_domains_merged_v1';

    public static function isEnabled(): bool
    {
        $settings = settings();

        if (! $settings) {
            return true;
        }

        if (! Schema::hasColumn('setting', 'block_disposable_email_registration')) {
            return true;
        }

        return (bool) ($settings->block_disposable_email_registration ?? true);
    }

    public static function isDisposable(?string $email): bool
    {
        if (! self::isEnabled()) {
            return false;
        }

        $domain = self::extractDomain($email);
        if ($domain === '') {
            return false;
        }

        foreach (self::blockedDomains() as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.'.$blocked)) {
                return true;
            }
        }

        return false;
    }

    public static function extractDomain(?string $email): string
    {
        $email = strtolower(trim((string) $email));
        if ($email === '' || ! str_contains($email, '@')) {
            return '';
        }

        $domain = substr($email, strrpos($email, '@') + 1);

        return self::normalizeDomain($domain);
    }

    public static function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = ltrim($domain, '@');
        $domain = rtrim($domain, '.');

        return $domain;
    }

    /**
     * @return list<string>
     */
    public static function blockedDomains(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $domains = config('disposable_email_domains', []);
            if (! is_array($domains)) {
                $domains = [];
            }

            $settings = settings();
            if ($settings && Schema::hasColumn('setting', 'blocked_email_domains')) {
                $domains = array_merge($domains, self::parseDomainList((string) ($settings->blocked_email_domains ?? '')));
            }

            $normalized = [];
            foreach ($domains as $domain) {
                $d = self::normalizeDomain((string) $domain);
                if ($d !== '' && self::isValidDomain($d)) {
                    $normalized[$d] = true;
                }
            }

            return array_keys($normalized);
        });
    }

    /**
     * @return list<string>
     */
    public static function parseDomainList(string $raw): array
    {
        if (trim($raw) === '') {
            return [];
        }

        $parts = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);

        return is_array($parts) ? $parts : [];
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function isValidDomain(string $domain): bool
    {
        return (bool) preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]*[a-z0-9])?)+$/', $domain);
    }
}
