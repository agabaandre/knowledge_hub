<?php

namespace Tests\Unit;

use App\Services\HttpNotificationsMailClient;
use App\Support\EmailConfig;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HttpNotificationsMailClientTest extends TestCase
{
    public function test_normalize_driver_accepts_http(): void
    {
        $method = new \ReflectionMethod(EmailConfig::class, 'normalizeDriver');
        $method->setAccessible(true);

        $this->assertSame('http', $method->invoke(null, 'http'));
        $this->assertSame('smtp', $method->invoke(null, 'smtp'));
        $this->assertSame('exchange', $method->invoke(null, 'exchange'));
        $this->assertSame('exchange', $method->invoke(null, 'unknown'));
    }

    public function test_send_authenticates_then_posts_to_integrations_send(): void
    {
        config([
            'emails.http.base_url' => 'https://notifications.test/api/v1',
            'emails.http.client_id' => 'client-1',
            'emails.http.client_secret' => 'secret-1',
        ]);

        Http::fake([
            'https://notifications.test/api/v1/integrations/auth/token' => Http::response([
                'token' => 'jwt-token',
                'expires_in' => 3600,
            ], 200),
            'https://notifications.test/api/v1/integrations/send' => Http::response(['ok' => true], 200),
        ]);

        $client = new HttpNotificationsMailClient();
        $client->send('user@example.com', 'Subject', '<p>Hello</p>');

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/integrations/auth/token')
                && $request['client_id'] === 'client-1'
                && $request['client_secret'] === 'secret-1';
        });

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/integrations/send')
                && $request['to'] === 'user@example.com'
                && $request['subject'] === 'Subject'
                && $request['is_html'] === true
                && $request->hasHeader('Authorization', 'Bearer jwt-token');
        });
    }

    public function test_is_configured_requires_client_credentials(): void
    {
        config([
            'emails.http.client_id' => '',
            'emails.http.client_secret' => '',
        ]);
        $this->assertFalse((new HttpNotificationsMailClient())->isConfigured());

        config([
            'emails.http.client_id' => 'id',
            'emails.http.client_secret' => 'secret',
        ]);
        $this->assertTrue((new HttpNotificationsMailClient())->isConfigured());
    }
}
