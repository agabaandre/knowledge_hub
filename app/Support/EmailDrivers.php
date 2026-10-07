<?php

namespace App\Support;

/**
 * Outbound mail drivers available in Admin → Configure → Email
 * (aligned with Staff Portal providers that can actually send).
 */
class EmailDrivers
{
    public const SUPPORTED = [
        'http',
        'exchange',
        'smtp',
        'zoho',
        'sendgrid',
        'mailgun',
        'postmark',
        'mailjet',
        'log',
    ];

    /**
     * @return list<array{key: string, label: string, category: string, panel: string, description: string}>
     */
    public static function definitions(): array
    {
        return [
            [
                'key' => 'http',
                'label' => 'Africa CDC Email Server (HTTP)',
                'category' => 'Africa CDC',
                'panel' => 'http',
                'description' => 'Central Africa CDC notifications gateway — same as Staff Portal.',
            ],
            [
                'key' => 'exchange',
                'label' => 'Microsoft Exchange / Graph',
                'category' => 'Microsoft',
                'panel' => 'exchange',
                'description' => 'Send via Microsoft Graph with an Entra ID app.',
            ],
            [
                'key' => 'smtp',
                'label' => 'SMTP',
                'category' => 'Generic',
                'panel' => 'smtp',
                'description' => 'Any standard SMTP server.',
            ],
            [
                'key' => 'zoho',
                'label' => 'Zoho Mail',
                'category' => 'Zoho',
                'panel' => 'smtp',
                'description' => 'Zoho SMTP (defaults to smtp.zoho.com:587).',
            ],
            [
                'key' => 'sendgrid',
                'label' => 'SendGrid',
                'category' => 'Transactional',
                'panel' => 'api',
                'description' => 'Twilio SendGrid Web API.',
            ],
            [
                'key' => 'mailgun',
                'label' => 'Mailgun',
                'category' => 'Transactional',
                'panel' => 'api',
                'description' => 'Mailgun Messages API.',
            ],
            [
                'key' => 'postmark',
                'label' => 'Postmark',
                'category' => 'Transactional',
                'panel' => 'api',
                'description' => 'Postmark server API token.',
            ],
            [
                'key' => 'mailjet',
                'label' => 'Mailjet',
                'category' => 'Transactional',
                'panel' => 'api',
                'description' => 'Mailjet Send API v3.1.',
            ],
            [
                'key' => 'log',
                'label' => 'Log only (development)',
                'category' => 'Generic',
                'panel' => 'log',
                'description' => 'Write messages to the application log instead of sending.',
            ],
        ];
    }

    public static function normalize(string $driver): string
    {
        $driver = strtolower(trim($driver));

        return in_array($driver, self::SUPPORTED, true) ? $driver : 'exchange';
    }

    public static function isSupported(string $driver): bool
    {
        return in_array(strtolower(trim($driver)), self::SUPPORTED, true);
    }

    public static function panelFor(string $driver): string
    {
        foreach (self::definitions() as $def) {
            if ($def['key'] === $driver) {
                return $def['panel'];
            }
        }

        return 'exchange';
    }

    /**
     * SMTP host/port defaults when the driver is Zoho and fields are empty.
     *
     * @return array{host: string, port: string, encryption: string}
     */
    public static function smtpDefaults(string $driver): array
    {
        if ($driver === 'zoho') {
            return [
                'host' => 'smtp.zoho.com',
                'port' => '587',
                'encryption' => 'tls',
            ];
        }

        return [
            'host' => '',
            'port' => '587',
            'encryption' => 'tls',
        ];
    }

    public static function usesSmtp(string $driver): bool
    {
        return in_array($driver, ['smtp', 'zoho'], true);
    }

    public static function usesApi(string $driver): bool
    {
        return in_array($driver, ['sendgrid', 'mailgun', 'postmark', 'mailjet'], true);
    }
}
