<?php

namespace Tests\Unit;

use App\Support\SecurityHeaders;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_required_security_headers_are_defined(): void
    {
        $headers = SecurityHeaders::all();

        $this->assertSame('nosniff', $headers['X-Content-Type-Options']);
        $this->assertSame('DENY', $headers['X-Frame-Options']);
        $this->assertSame("default-src 'none'", $headers['Content-Security-Policy']);
        $this->assertSame('no-referrer', $headers['Referrer-Policy']);
        $this->assertSame('max-age=31536000', $headers['Strict-Transport-Security']);
        $this->assertSame('geolocation=(), microphone=(), camera=()', $headers['Permissions-Policy']);
    }

    public function test_next_proxy_and_apache_apply_the_same_headers(): void
    {
        $next = file_get_contents(base_path('front_end/next.config.ts'));
        $proxy = file_get_contents(base_path('front_end/front-end-proxy.php'));
        $apache = file_get_contents(base_path('front_end/apache-front-end.conf'));
        $nginx = file_get_contents(base_path('docker/nginx/default.conf'));
        $htaccess = file_get_contents(public_path('.htaccess'));

        foreach (['X-Content-Type-Options', 'X-Frame-Options', 'Content-Security-Policy', 'Referrer-Policy', 'Strict-Transport-Security', 'Permissions-Policy'] as $header) {
            $this->assertStringContainsString($header, $next);
            $this->assertStringContainsString($header, $apache);
            $this->assertStringContainsString($header, $nginx);
            $this->assertStringContainsString($header, $htaccess);
        }

        $this->assertStringContainsString("default-src 'none'", $next);
        $this->assertStringContainsString('SecurityHeaders', $proxy);
    }
}
