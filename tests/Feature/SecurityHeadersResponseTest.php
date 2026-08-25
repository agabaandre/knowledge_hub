<?php

namespace Tests\Feature;

use App\Http\Middleware\BotProtection;
use Tests\TestCase;

class SecurityHeadersResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(BotProtection::class);
    }

    public function test_api_responses_include_required_security_headers(): void
    {
        $response = $this->getJson('/api/users/me');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Content-Security-Policy', "default-src 'none'");
        $response->assertHeader('Referrer-Policy', 'no-referrer');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000');
        $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }
}
