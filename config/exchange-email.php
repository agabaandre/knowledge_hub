<?php

return [
    'tenant_id' => env('EXCHANGE_TENANT_ID'),
    'client_id' => env('EXCHANGE_CLIENT_ID'),
    'client_secret' => env('EXCHANGE_CLIENT_SECRET'),
    'redirect_uri' => env('EXCHANGE_REDIRECT_URI', env('APP_URL') . '/oauth/callback'),
    'scope' => env('EXCHANGE_SCOPE', 'https://graph.microsoft.com/Mail.Send'),
    'auth_method' => env('EXCHANGE_AUTH_METHOD', 'client_credentials'),
];

