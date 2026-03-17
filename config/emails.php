<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email sending driver
    |--------------------------------------------------------------------------
    | Single source of truth: set EMAIL_DRIVER or MAIL_MAILER in .env to
    | 'exchange' or 'smtp'. EMAIL_DRIVER takes precedence over MAIL_MAILER.
    | - exchange: uses Microsoft Graph (requires EXCHANGE_TENANT_ID, etc.)
    | - smtp: uses PHPMailer/SMTP (requires MAIL_HOST, MAIL_USERNAME, etc.)
    | Applies to password reset, queued mail, and all send_email() usage.
    | After changing .env run: php artisan config:clear
    */
    'driver'      => env('EMAIL_DRIVER', env('MAIL_MAILER', 'exchange')),

    'host'        => env('MAIL_HOST'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),
    "smtp_secure" => env('MAIL_ENCRYPTION','ssl'),
    "port"        => env('MAIL_PORT','465'),
    "sender"      => env('MAIL_SENDERNAME')

];