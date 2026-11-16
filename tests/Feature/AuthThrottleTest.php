<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthThrottleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('auth-ip:127.0.0.1');
    }

    public function test_social_login_is_throttled_per_ip(): void
    {
        $statuses = [];
        for ($i = 0; $i < 7; $i++) {
            $statuses[] = $this->postJson('/api/social-login', ['provider' => 'google'])->status();
        }

        $this->assertContains(429, $statuses, 'Expected a 429 after repeated social-login attempts');
        $this->assertLessThanOrEqual(5, count(array_filter($statuses, fn ($s) => $s !== 429)));
    }

    public function test_login_is_throttled_per_target_email(): void
    {
        $email = 'victim-'.uniqid().'@example.org';
        $statuses = [];
        for ($i = 0; $i < 7; $i++) {
            $statuses[] = $this->postJson('/api/login', ['email' => $email, 'password' => 'guess-'.$i])->status();
        }

        $this->assertContains(429, $statuses, 'Expected a 429 after repeated login attempts on one account');
    }
}
