<?php

namespace Tests\Unit;

use App\Support\Social\SocialTokenVerificationException;
use App\Support\Social\SocialTokenVerifier;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SocialTokenVerifierTest extends TestCase
{
    private string $privatePem;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.google.client_id' => 'client-123', 'services.google.allowed_client_ids' => 'mobile-456']);

        $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $privatePem = '';
        openssl_pkey_export($resource, $privatePem);
        $this->privatePem = $privatePem;
        $details = openssl_pkey_get_details($resource);

        $b64url = static fn (string $bin) => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
        Http::fake([
            'https://www.googleapis.com/oauth2/v3/certs' => Http::response([
                'keys' => [[
                    'kty' => 'RSA', 'alg' => 'RS256', 'use' => 'sig', 'kid' => 'test-key',
                    'n' => $b64url($details['rsa']['n']),
                    'e' => $b64url($details['rsa']['e']),
                ]],
            ]),
        ]);
    }

    private function googleToken(array $overrides = []): string
    {
        $claims = array_merge([
            'iss' => 'https://accounts.google.com',
            'aud' => 'client-123',
            'sub' => 'google-sub-1',
            'email' => 'person@example.org',
            'email_verified' => true,
            'name' => 'Person Example',
            'iat' => time(),
            'exp' => time() + 600,
        ], $overrides);

        return JWT::encode($claims, $this->privatePem, 'RS256', 'test-key');
    }

    public function test_a_valid_google_id_token_yields_the_identity(): void
    {
        $identity = (new SocialTokenVerifier())->verify('google', $this->googleToken(), null);

        $this->assertSame('google-sub-1', $identity->subject);
        $this->assertSame('person@example.org', $identity->email);
        $this->assertTrue($identity->emailVerified);
    }

    public function test_mobile_client_id_is_accepted_as_audience(): void
    {
        $identity = (new SocialTokenVerifier())->verify('google', $this->googleToken(['aud' => 'mobile-456']), null);

        $this->assertSame('google-sub-1', $identity->subject);
    }

    public function test_wrong_audience_is_rejected(): void
    {
        $this->expectException(SocialTokenVerificationException::class);
        (new SocialTokenVerifier())->verify('google', $this->googleToken(['aud' => 'someone-elses-app']), null);
    }

    public function test_wrong_issuer_is_rejected(): void
    {
        $this->expectException(SocialTokenVerificationException::class);
        (new SocialTokenVerifier())->verify('google', $this->googleToken(['iss' => 'https://evil.example']), null);
    }

    public function test_expired_token_is_rejected(): void
    {
        $this->expectException(SocialTokenVerificationException::class);
        (new SocialTokenVerifier())->verify('google', $this->googleToken(['exp' => time() - 600, 'iat' => time() - 1200]), null);
    }

    public function test_token_signed_with_another_key_is_rejected(): void
    {
        $other = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($other, $otherPem);
        $forged = JWT::encode([
            'iss' => 'https://accounts.google.com', 'aud' => 'client-123', 'sub' => 'x',
            'email' => 'x@example.org', 'email_verified' => true, 'exp' => time() + 600,
        ], $otherPem, 'RS256', 'test-key');

        $this->expectException(SocialTokenVerificationException::class);
        (new SocialTokenVerifier())->verify('google', $forged, null);
    }

    public function test_missing_token_is_rejected(): void
    {
        $this->expectException(SocialTokenVerificationException::class);
        (new SocialTokenVerifier())->verify('google', null, null);
    }
}
