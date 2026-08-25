<?php

/**
 * Locales aligned with profile / language selector (users.langauge, Google Translate codes).
 */
return [

    'default' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Right-to-left UI locales (layout direction; Google Translate unchanged)
    |--------------------------------------------------------------------------
    */
    'rtl_locales' => ['ar'],

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
        'frontend_nav' => 'Frontend navigation (main menu, browse categories & key links)',
        'admin_nav' => 'Admin sidebar menu',
        'ui_body' => 'Account menu, site title/tagline, footer & static body chrome',
        'home_sections' => 'Homepage sections, search & filters',
    ],

    /*
    | Groups refreshed via AJAX when the user changes language (not Google Translate).
    */
    'ui_native_groups' => [
        'frontend_nav',
        'ui_body',
        'home_sections',
    ],

    /*
    | Optional admin setting fields used as fallback when a home_sections key has no translation.
    */
    'home_section_settings' => [
        'recommended' => 'section_title_recommended',
        'top_searches' => 'section_title_top_searches',
        'flagship_initiatives' => 'section_title_flagship_initiatives',
        'health_themes' => 'section_title_health_themes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie set when user picks a language (Laravel locale, complements googtrans)
    |--------------------------------------------------------------------------
    */
    'locale_cookie' => 'khub_locale',

    'locale_cookie_minutes' => 60 * 24 * 365,

    /*
    | Cookie path for locale / Google Translate (subdir installs e.g. /knowledge_hub/).
    */
    'cookie_path' => ($__localeCookiePath = parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_PATH))
        && $__localeCookiePath !== '/'
        && $__localeCookiePath !== ''
        ? rtrim($__localeCookiePath, '/').'/'
        : '/',
];
