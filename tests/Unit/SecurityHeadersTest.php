<?php

namespace Tests\Unit;

use App\Support\SecurityHeaders;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_required_security_headers_are_defined(): void
    {
        $headers = SecurityHeaders::web();

        $this->assertSame('nosniff', $headers['X-Content-Type-Options']);
        $this->assertSame('DENY', $headers['X-Frame-Options']);
        $this->assertStringContainsString("default-src 'self'", $headers['Content-Security-Policy']);
        $this->assertStringContainsString('script-src', $headers['Content-Security-Policy']);
        $this->assertStringContainsString('style-src', $headers['Content-Security-Policy']);
        $this->assertStringContainsString('img-src', $headers['Content-Security-Policy']);
        $this->assertStringNotContainsString("default-src 'none'", $headers['Content-Security-Policy']);
        $this->assertSame('no-referrer', $headers['Referrer-Policy']);
        $this->assertSame('max-age=31536000', $headers['Strict-Transport-Security']);
        $this->assertSame('geolocation=(), microphone=(), camera=()', $headers['Permissions-Policy']);
        $this->assertSame("default-src 'none'", SecurityHeaders::api()['Content-Security-Policy']);
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

        $this->assertStringContainsString("default-src 'self'", $next);
        $this->assertStringContainsString("default-src 'self'", $htaccess);
        $this->assertStringContainsString("default-src 'self'", $nginx);
        $this->assertStringNotContainsString("default-src 'none'", $htaccess);
        $this->assertStringContainsString('SecurityHeaders', $proxy);
    }

    public function test_html_responses_use_web_csp_and_json_api_keeps_none(): void
    {
        $middleware = new \App\Http\Middleware\SetSecurityHeaders();

        $html = $middleware->handle(
            \Illuminate\Http\Request::create('/records', 'GET'),
            static fn () => response('<html></html>', 200, ['Content-Type' => 'text/html; charset=UTF-8'])
        );
        $this->assertStringContainsString("default-src 'self'", (string) $html->headers->get('Content-Security-Policy'));
        $this->assertStringNotContainsString("default-src 'none'", (string) $html->headers->get('Content-Security-Policy'));

        $api = $middleware->handle(
            \Illuminate\Http\Request::create('/api/users/me', 'GET'),
            static fn () => response()->json(['status' => 401], 401)
        );
        $this->assertSame("default-src 'none'", $api->headers->get('Content-Security-Policy'));
    }
}
