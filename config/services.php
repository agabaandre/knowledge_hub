<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'recaptcha' => [
    'site_key' => env('NOCAPTCHA_SITEKEY'),
    'secret_key' => env('NOCAPTCHA_SECRET'),
    ],
    
    /*
    | Microsoft (Azure AD) - used for "Sign in with Microsoft" on login/register.
    | If you use the same Azure app for both login and Exchange email, you can
    | set only EXCHANGE_* in .env and leave MICROSOFT_* unset; they will fall back.
    | Redirect must match the redirect URI registered in Azure (e.g. .../auth/microsoft/callback).
    */
    'microsoft' => [
        'client_id'     => env('MICROSOFT_CLIENT_ID', env('EXCHANGE_CLIENT_ID')),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET', env('EXCHANGE_CLIENT_SECRET')),
        'redirect'      => env('MICROSOFT_REDIRECT_URI', env('APP_URL') . '/auth/microsoft/callback'),
        'tenant'        => env('MICROSOFT_TENANT_ID', env('EXCHANGE_TENANT_ID', 'common')),
    ],

    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'linkedin' => [
    'client_id' => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'redirect' => env('LINKEDIN_REDIRECT_URI'),
    'scopes' => ['openid', 'profile', 'email'],
    ],

    'linkedin-openid' => [
    'client_id' => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'redirect' => env('LINKEDIN_REDIRECT_URI'),
    ],

    /*
    | Path to LibreOffice "soffice" for converting forum comment attachments (doc, ppt, xlsx, …) to PDF.
    | Leave null to auto-detect (common Linux paths and macOS app bundle).
    */
    'libreoffice' => [
        'binary' => env('LIBREOFFICE_BINARY'),
    ],

];
