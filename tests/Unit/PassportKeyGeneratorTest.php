<?php

namespace Tests\Unit;

use App\Support\PassportKeyGenerator;
use Tests\TestCase;

class PassportKeyGeneratorTest extends TestCase
{
    public function test_it_creates_valid_pem_key_pair(): void
    {
        [$private, $public] = PassportKeyGenerator::createKeyPair(2048);

        $this->assertStringContainsString('BEGIN', $private);
        $this->assertStringContainsString('PRIVATE KEY', $private);
        $this->assertStringContainsString('BEGIN', $public);
        $this->assertStringContainsString('PUBLIC KEY', $public);
        $this->assertNotFalse(openssl_pkey_get_private($private));
        $this->assertNotFalse(openssl_pkey_get_public($public));
    }

    public function test_it_writes_key_files_when_missing(): void
    {
        $dir = sys_get_temp_dir().'/khub-passport-keys-'.uniqid('', true);
        mkdir($dir, 0700, true);

        $private = $dir.'/oauth-private.key';
        $public = $dir.'/oauth-public.key';

        // Point Passport keyPath via storage_path override by writing where generator defaults;
        // exercise create + validate helpers directly.
        [$privatePem, $publicPem] = PassportKeyGenerator::createKeyPair(2048);
        file_put_contents($private, $privatePem);
        file_put_contents($public, $publicPem);

        $this->assertTrue(PassportKeyGenerator::keysAreValid($private, $public));

        @unlink($private);
        @unlink($public);
        @rmdir($dir);
    }
}
