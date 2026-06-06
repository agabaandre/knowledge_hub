<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email sending driver
    |--------------------------------------------------------------------------
    | Priority: .env values first; when empty, admin Email settings (database).
    | Set EMAIL_DRIVER or MAIL_MAILER in .env to 'exchange' or 'smtp'.
    | EMAIL_DRIVER takes precedence over MAIL_MAILER. Default: exchange.
    | - exchange: Microsoft Graph (EXCHANGE_TENANT_ID, etc.)
    | - smtp: PHPMailer/SMTP (MAIL_HOST, MAIL_USERNAME, etc.)
    | Runtime resolution: App\Support\EmailConfig::applyRuntimeConfig()
    */
    'driver'      => env('EMAIL_DRIVER', env('MAIL_MAILER', 'exchange')),

    'host'        => env('MAIL_HOST'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),
    "smtp_secure" => env('MAIL_ENCRYPTION','ssl'),
    "port"        => env('MAIL_PORT','465'),
    "sender"      => env('MAIL_SENDERNAME')

];