<?php

// Single source for Africa CDC HTTP mail defaults — do not copy these URLs elsewhere.
$httpDefaultBaseUrl = 'https://notifications.africacdc.org/api/v1';
$httpDefaultDocsUrl = 'https://notifications.africacdc.org/api/documentation';

return [
    /*
    |--------------------------------------------------------------------------
    | Email sending driver
    |--------------------------------------------------------------------------
    | Priority: .env values first; when empty, admin Email settings (database).
    | Set EMAIL_DRIVER or MAIL_MAILER in .env.
    | Supported: http, exchange, smtp, zoho, sendgrid, mailgun, postmark, mailjet, log
    | EMAIL_DRIVER takes precedence over MAIL_MAILER. Default: exchange.
    | Runtime resolution: App\Support\EmailConfig::applyRuntimeConfig()
    */
    'driver'      => env('EMAIL_DRIVER', env('MAIL_MAILER', 'exchange')),

    'host'        => env('MAIL_HOST'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),
    'smtp_secure' => env('MAIL_ENCRYPTION', 'ssl'),
    'port'        => env('MAIL_PORT', '465'),
    'sender'      => env('MAIL_SENDERNAME'),
    'from_address'=> env('MAIL_FROM_ADDRESS'),

    /*
    | Africa CDC Email Server (HTTP)
    | default_base_url is the single hardcoded default; everywhere else uses config().
    | Override with MAIL_HTTP_BASE_URL or Admin → Configure → Email.
    */
    'http' => [
        'default_base_url' => $httpDefaultBaseUrl,
        'base_url' => env('MAIL_HTTP_BASE_URL', $httpDefaultBaseUrl),
        'docs_url' => env('MAIL_HTTP_DOCS_URL', $httpDefaultDocsUrl),
        'client_id' => env('MAIL_HTTP_CLIENT_ID'),
        'client_secret' => env('MAIL_HTTP_CLIENT_SECRET'),
    ],

    /*
    | Transactional API providers (SendGrid / Mailgun / Postmark / Mailjet)
    */
    'api' => [
        'key' => env('MAIL_API_KEY'),
        'secret' => env('MAIL_API_SECRET'),
        'domain' => env('MAIL_API_DOMAIN'),
        'region' => env('MAIL_API_REGION', 'us'),
        'base_url' => env('MAIL_API_BASE_URL'),
        'message_stream' => env('MAIL_API_MESSAGE_STREAM', 'outbound'),
    ],
];
