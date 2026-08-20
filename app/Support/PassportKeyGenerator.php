<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use phpseclib\Crypt\RSA as LegacyRSA;
use phpseclib3\Crypt\RSA;
use Throwable;

/**
 * Create storage/oauth-*.key without relying on `php artisan passport:keys`.
 * Artisan::call() during AuthServiceProvider::boot() often fails with
 * "The command passport:keys does not exist" on country hubs.
 */
class PassportKeyGenerator
{
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

        return self::isReadablePem($private) && self::isReadablePem($public);
    }

    public static function ensureKeysExist(int $bits = 4096): bool
    {
        $private = self::privateKeyPath();
        $public = self::publicKeyPath();

        if (self::keysAreValid($private, $public)) {
            self::securePermissions($private, $public);

            return true;
        }

        try {
            [$privatePem, $publicPem] = self::createKeyPair($bits);

            if ($privatePem === '' || $publicPem === '') {
                throw new \RuntimeException('Generated empty OAuth key material.');
            }

            if (! is_dir(dirname($private))) {
                @mkdir(dirname($private), 0755, true);
            }

            if (file_put_contents($private, $privatePem) === false
                || file_put_contents($public, $publicPem) === false) {
                throw new \RuntimeException('Unable to write OAuth key files to storage.');
            }

            self::securePermissions($private, $public);

            return self::keysAreValid($private, $public);
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

    protected static function isReadablePem(string $path): bool
    {
        if (! is_file($path) || ! is_readable($path) || filesize($path) <= 0) {
            return false;
        }

        $contents = (string) @file_get_contents($path);

        return str_contains($contents, 'BEGIN') && str_contains($contents, 'KEY');
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
}
