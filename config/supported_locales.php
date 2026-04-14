<?php

/**
 * Locales aligned with profile / language selector (users.langauge, Google Translate codes).
 */
return [

    'default' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Supported UI locales (key = stored value in users.langauge / cookie)
    |--------------------------------------------------------------------------
    */
    'languages' => [
        'en' => ['name' => 'English', 'flag' => '🇺🇸', 'code' => 'en'],
        'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'code' => 'fr'],
        'ar' => ['name' => 'العربية', 'flag' => '🇸🇦', 'code' => 'ar'],
        'es' => ['name' => 'Español', 'flag' => '🇪🇸', 'code' => 'es'],
        'pt' => ['name' => 'Português', 'flag' => '🇵🇹', 'code' => 'pt'],
        'sw' => ['name' => 'Kiswahili', 'flag' => '🇰🇪', 'code' => 'sw'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation file groups editable under Settings → Language management
    |--------------------------------------------------------------------------
    */
    'ui_groups' => [
        'frontend_nav' => 'Frontend navigation (main menu)',
        'admin_nav' => 'Admin sidebar menu',
        'ui_body' => 'Account menu, footer & static body chrome',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie set when user picks a language (Laravel locale, complements googtrans)
    |--------------------------------------------------------------------------
    */
    'locale_cookie' => 'khub_locale',

    'locale_cookie_minutes' => 60 * 24 * 365,
];
