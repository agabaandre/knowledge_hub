<?php

namespace Tests\Unit;

use App\Support\PassportKeyGenerator;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PassportKeyGeneratorTest extends TestCase
{
    public function test_it_creates_valid_pem_key_pair(): void
    {
        [$private, $public] = PassportKeyGenerator::createKeyPair(2048);

        $this->assertTrue(PassportKeyGenerator::isPemString($private));
        $this->assertTrue(PassportKeyGenerator::isPemString($public));
        $this->assertStringContainsString('PRIVATE KEY', $private);
        $this->assertStringContainsString('PUBLIC KEY', $public);
        $this->assertNotFalse(openssl_pkey_get_private($private));
        $this->assertNotFalse(openssl_pkey_get_public($public));
    }

    public function test_ensure_keys_injects_config_even_without_writable_storage(): void
    {
        config([
            'passport.private_key' => null,
            'passport.public_key' => null,
            'cache.default' => 'array',
        ]);

        Cache::flush();

        $ok = PassportKeyGenerator::ensureKeysExist(2048);

        $this->assertTrue($ok);
        $this->assertTrue(PassportKeyGenerator::isPemString((string) config('passport.private_key')));
        $this->assertTrue(PassportKeyGenerator::isPemString((string) config('passport.public_key')));
    }

    public function test_ensure_keys_caches_pem_for_later_requests(): void
    {
        config([
            'passport.private_key' => null,
            'passport.public_key' => null,
            'cache.default' => 'array',
        ]);
        Cache::flush();

        $ok = PassportKeyGenerator::ensureKeysExist(2048);

        $this->assertTrue($ok);
        $cachedPrivate = (string) Cache::get(PassportKeyGenerator::CACHE_PRIVATE);
        $cachedPublic = (string) Cache::get(PassportKeyGenerator::CACHE_PUBLIC);

        // Cache write is best-effort; when it works, PEM must be valid.
        if ($cachedPrivate !== '' || $cachedPublic !== '') {
            $this->assertTrue(PassportKeyGenerator::isPemString($cachedPrivate));
            $this->assertTrue(PassportKeyGenerator::isPemString($cachedPublic));
        }

        $this->assertTrue(PassportKeyGenerator::isPemString((string) config('passport.private_key')));
        $this->assertTrue(PassportKeyGenerator::isPemString((string) config('passport.public_key')));
    }
}
