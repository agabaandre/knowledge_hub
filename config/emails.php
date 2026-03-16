
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email sending driver
    |--------------------------------------------------------------------------
    | Use 'exchange' for Microsoft Graph / Exchange OAuth, or 'smtp' for
    | PHPMailer/SMTP. When 'exchange', Exchange must be configured in .env
    | (EXCHANGE_TENANT_ID, EXCHANGE_CLIENT_ID, EXCHANGE_CLIENT_SECRET).
    */
    'driver'      => env('EMAIL_DRIVER', 'exchange'),

    'host'        => env('MAIL_HOST'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),
    "smtp_secure" => env('MAIL_ENCRYPTION','ssl'),
    "port"        => env('MAIL_PORT','465'),
    "sender"      => env('MAIL_SENDERNAME')

];