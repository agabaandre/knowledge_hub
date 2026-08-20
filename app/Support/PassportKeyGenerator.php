<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use phpseclib\Crypt\RSA as LegacyRSA;
use phpseclib3\Crypt\RSA;
use Throwable;

/**
 * Ensure Passport has usable RSA material even when:
 * - `php artisan passport:keys` is unavailable during provider boot
 * - storage/oauth-*.key cannot be written (common on country hubs)
 *
 * Prefer files, then cache, then in-process config PEM (Passport accepts
 * passport.private_key / passport.public_key as raw PEM).
 */
class PassportKeyGenerator
{
    public const CACHE_PRIVATE = 'passport.oauth_private_key_pem';

    public const CACHE_PUBLIC = 'passport.oauth_public_key_pem';

    public static function privateKeyPath(): string
    {
        return class_exists(Passport::class)
            ? Passport::keyPath('oauth-private.key')
            : storage_path('oauth-private.key');
    }

    public static function publicKeyPath(): string
    {
        return class_exists(Passport::class)
            ? Passport::keyPath('oauth-public.key')
            : storage_path('oauth-public.key');
    }

    public static function keysAreValid(?string $private = null, ?string $public = null): bool
    {
        $private = $private ?? self::privateKeyPath();
        $public = $public ?? self::publicKeyPath();

        return self::isReadablePemFile($private) && self::isReadablePemFile($public);
    }

    public static function ensureKeysExist(int $bits = 4096): bool
    {
        try {
            // 1) Already configured as PEM (env / previous apply)
            $cfgPrivate = (string) (config('passport.private_key') ?? '');
            $cfgPublic = (string) (config('passport.public_key') ?? '');
            if (self::isPemString($cfgPrivate) && self::isPemString($cfgPublic)) {
                return true;
            }

            // 2) Existing key files
            $privatePath = self::privateKeyPath();
            $publicPath = self::publicKeyPath();
            if (self::keysAreValid($privatePath, $publicPath)) {
                self::securePermissions($privatePath, $publicPath);
                self::applyToConfig(
                    (string) file_get_contents($privatePath),
                    (string) file_get_contents($publicPath)
                );

                return true;
            }

            // 3) Cached PEM (Redis/file) when storage root is not writable
            $cachedPrivate = self::cacheGet(self::CACHE_PRIVATE);
            $cachedPublic = self::cacheGet(self::CACHE_PUBLIC);
            if (self::isPemString($cachedPrivate) && self::isPemString($cachedPublic)) {
                self::applyToConfig($cachedPrivate, $cachedPublic);
                self::persistKeysBestEffort($cachedPrivate, $cachedPublic);

                return true;
            }

            // 4) Generate fresh material
            [$privatePem, $publicPem] = self::createKeyPair($bits);
            if (! self::isPemString($privatePem) || ! self::isPemString($publicPem)) {
                throw new \RuntimeException('Generated empty OAuth key material.');
            }

            self::applyToConfig($privatePem, $publicPem);

            $persisted = self::persistKeysBestEffort($privatePem, $publicPem);
            $cached = self::cachePut($privatePem, $publicPem);

            if (! $persisted && ! $cached) {
                Log::warning(
                    'Passport OAuth keys are active for this process only; '
                    .'could not write storage/oauth-*.key or cache. '
                    .'Fix ownership: chown -R www-data:www-data storage bootstrap/cache'
                );
            } elseif (! $persisted) {
                Log::warning(
                    'Passport OAuth keys stored in cache only; storage/oauth-*.key is not writable. '
                    .'Run: chown -R www-data:www-data /var/www/ghana/storage && chmod 775 storage'
                );
            }

            return true;
        } catch (Throwable $e) {
            Log::error('Failed to generate Passport OAuth keys: '.$e->getMessage());

            return false;
        }
    }

    /**
     * @return array{0: string, 1: string} [privatePem, publicPem]
     */
    public static function createKeyPair(int $bits = 4096): array
    {
        if (class_exists(LegacyRSA::class)) {
            $keys = (new LegacyRSA)->createKey($bits);

            return [
                (string) Arr::get($keys, 'privatekey', ''),
                (string) Arr::get($keys, 'publickey', ''),
            ];
        }

        if (class_exists(RSA::class)) {
            $key = RSA::createKey($bits);

            return [
                (string) $key,
                (string) $key->getPublicKey(),
            ];
        }

        $resource = openssl_pkey_new([
            'private_key_bits' => $bits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if ($resource === false) {
            throw new \RuntimeException('openssl_pkey_new failed: '.((string) openssl_error_string()));
        }

        $privatePem = '';
        if (! openssl_pkey_export($resource, $privatePem)) {
            throw new \RuntimeException('openssl_pkey_export failed: '.((string) openssl_error_string()));
        }

        $details = openssl_pkey_get_details($resource);
        $publicPem = is_array($details) ? (string) ($details['key'] ?? '') : '';

        if ($publicPem === '') {
            throw new \RuntimeException('Failed to derive OAuth public key from OpenSSL.');
        }

        return [$privatePem, $publicPem];
    }

    public static function applyToConfig(string $privatePem, string $publicPem): void
    {
        config([
            'passport.private_key' => $privatePem,
            'passport.public_key' => $publicPem,
        ]);
    }

    /**
     * Try several writable directories so hubs with root-owned storage/ still work.
     */
    public static function persistKeysBestEffort(string $privatePem, string $publicPem): bool
    {
        $dirs = array_values(array_unique([
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('framework/cache'),
        ]));

        foreach ($dirs as $dir) {
            if (! is_dir($dir) && ! @mkdir($dir, 0775, true) && ! is_dir($dir)) {
                continue;
            }
            if (! is_writable($dir)) {
                continue;
            }

            $private = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'oauth-private.key';
            $public = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'oauth-public.key';

            $wrotePrivate = @file_put_contents($private, $privatePem);
            $wrotePublic = @file_put_contents($public, $publicPem);
            if ($wrotePrivate === false || $wrotePublic === false) {
                continue;
            }

            self::securePermissions($private, $public);

            if ($dir !== storage_path() && class_exists(Passport::class)) {
                Passport::loadKeysFrom($dir);
            }

            return true;
        }

        return false;
    }

    public static function isPemString(string $contents): bool
    {
        $contents = trim($contents);

        return $contents !== ''
            && str_contains($contents, 'BEGIN')
            && str_contains($contents, 'KEY');
    }

    protected static function isReadablePemFile(string $path): bool
    {
        if (! is_file($path) || ! is_readable($path) || filesize($path) <= 0) {
            return false;
        }

        return self::isPemString((string) @file_get_contents($path));
    }

    protected static function securePermissions(string $private, string $public): void
    {
        if (is_file($private)) {
            @chmod($private, 0600);
        }
        if (is_file($public)) {
            @chmod($public, 0600);
        }
    }

    protected static function cacheGet(string $key): string
    {
        try {
            return (string) (Cache::get($key) ?? '');
        } catch (Throwable) {
            return '';
        }
    }

    protected static function cachePut(string $privatePem, string $publicPem): bool
    {
        try {
            Cache::forever(self::CACHE_PRIVATE, $privatePem);
            Cache::forever(self::CACHE_PUBLIC, $publicPem);

            return self::isPemString(self::cacheGet(self::CACHE_PRIVATE))
                && self::isPemString(self::cacheGet(self::CACHE_PUBLIC));
        } catch (Throwable $e) {
            Log::warning('Could not cache Passport OAuth keys: '.$e->getMessage());

            return false;
        }
    }
}
