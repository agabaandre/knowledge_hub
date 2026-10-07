<?php

namespace Tests\Unit;

use App\Services\TransactionalMailClient;
use App\Support\EmailDrivers;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailDriversTest extends TestCase
{
    public function test_supported_drivers_include_zoho_and_transactional_apis(): void
    {
        foreach (['http', 'exchange', 'smtp', 'zoho', 'sendgrid', 'mailgun', 'postmark', 'mailjet', 'log'] as $driver) {
            $this->assertTrue(EmailDrivers::isSupported($driver), $driver);
        }

        $this->assertSame('smtp', EmailDrivers::panelFor('zoho'));
        $this->assertSame('api', EmailDrivers::panelFor('sendgrid'));
        $this->assertSame('smtp.zoho.com', EmailDrivers::smtpDefaults('zoho')['host']);
    }

    public function test_sendgrid_posts_to_mail_send(): void
    {
        config([
            'emails.from_address' => 'noreply@example.com',
            'emails.sender' => 'KHub',
            'emails.api.key' => 'sg-key',
            'emails.api.base_url' => 'https://api.sendgrid.com/v3',
        ]);

        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => Http::response([], 202),
        ]);

        (new TransactionalMailClient())->send('sendgrid', 'user@example.com', 'Hi', '<p>Body</p>');

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/mail/send')
                && $request['subject'] === 'Hi'
                && $request->hasHeader('Authorization', 'Bearer sg-key');
        });
    }
}
