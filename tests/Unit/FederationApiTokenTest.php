<?php

namespace Tests\Unit;

use App\Support\FederationApiToken;
use Tests\TestCase;

class FederationApiTokenTest extends TestCase
{
    public function test_random_token_is_url_safe_and_long_enough_for_bearer_auth(): void
    {
        $token = FederationApiToken::random();

        $this->assertSame(FederationApiToken::LENGTH, strlen($token));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $token);
        $this->assertNotSame($token, FederationApiToken::random());
    }

    public function test_admin_token_uses_the_same_shared_secret_format(): void
    {
        $token = FederationApiToken::forAdmin(null);

        $this->assertSame(FederationApiToken::LENGTH, strlen($token));
    }
}
