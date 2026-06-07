<?php

return [
    'lock_file' => storage_path('app/installed.lock'),

    'required_php' => '8.0.0',

    'required_extensions' => [
        'bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'json', 'mbstring',
        'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'zip', 'gd', 'intl',
    ],

    'recommended_extensions' => [
        'redis' => 'Redis cache/sessions (optional; file/database drivers work without it)',
        'exif' => 'Image metadata for uploads',
    ],

    'writable_paths' => [
        storage_path(),
        storage_path('app'),
        storage_path('framework'),
        storage_path('framework/cache'),
        storage_path('framework/sessions'),
        storage_path('framework/views'),
        storage_path('logs'),
        base_path('bootstrap/cache'),
        public_path('uploads'),
    ],

    'storage_env_keys' => [
        'HUB_SITE_ID',
        'HUB_FILES_ROOT',
        'HUB_SQL_BACKUP_ROOT',
    ],

    'central_hub_env_keys' => [
        'CENTRAL_HUB_URL',
        'CENTRAL_HUB_API_TOKEN',
    ],

    'mail_env_keys' => [
        'MAIL_MAILER',
        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_USERNAME',
        'MAIL_PASSWORD',
        'MAIL_ENCRYPTION',
        'MAIL_FROM_ADDRESS',
        'MAIL_FROM_NAME',
    ],

    'exchange_env_keys' => [
        'EXCHANGE_TENANT_ID',
        'EXCHANGE_CLIENT_ID',
        'EXCHANGE_CLIENT_SECRET',
        'EXCHANGE_REDIRECT_URI',
        'EXCHANGE_SCOPE',
        'EXCHANGE_AUTH_METHOD',
    ],

    'database_defaults' => [
        'docker' => [
            'host' => 'mysql',
            'port' => '3306',
            'database' => 'knowledge_hub',
            'username' => 'root',
            'password' => 'password',
        ],
        'local' => [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'knowledge_hub',
            'username' => 'root',
            'password' => 'password',
        ],
        'windows' => [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'knowledge_hub',
            'username' => 'root',
            'password' => '',
        ],
    ],

    /*
    | Host paths for hub files/SQL backups. Empty values use HubStorageService defaults
    | (/var/khubdata/{site-id}/… on Linux/macOS, C:\khubdata\{site-id}\… on Windows).
    */
    'storage_defaults' => [
        'local' => [],
        'docker' => [],
        'windows' => [],
    ],

    'timezones' => [
        'UTC',
        'Africa/Nairobi',
        'Africa/Addis_Ababa',
        'Africa/Johannesburg',
        'Africa/Lagos',
        'Africa/Cairo',
        'Europe/London',
        'America/New_York',
    ],

    /**
     * Defaults merged when creating/updating the active setting row during install.
     */
    'default_site' => [
        'config_name' => 'Default',
        'status' => 'active',
        'language' => 'english',
        'timezone' => 'Africa/Nairobi',
        'default_primary_color' => '#563D7C',
        'default_secondary_color' => '#2F2424',
        'icon_font_color' => '#007749',
        'primary_color' => '#006239',
        'secondary_color' => '#413C3C',
        'gradient_start_color' => '#87C8A1',
        'gradient_end_color' => '#238444',
        'au_red' => '#9F2241',
        'au_gold' => '#B4A269',
        'au_corporate_green' => '#1A5632',
        'au_green' => '#1A5632',
        'au_plum' => '#522B39',
        'au_grey_text' => '#58595B',
        'au_white' => '#FFFFFF',
        'auto_approve_comments' => 1,
        'enable_ai_search' => 1,
        'enable_ai_chat_prune' => 1,
        'menu_icons_enabled' => 1,
        'footer_style' => 'dark-footer',
        'show_featured' => 1,
        'show_events' => 1,
        'show_top_searches' => 1,
        'show_tags' => 1,
        'show_quotes' => 1,
        'show_health_themes' => 1,
        'theme_cards_per_row' => 4,
        'theme_card_opacity' => '0.6',
        'publication_min_words' => 150,
        'enable_version_submission' => 1,
        'enable_microsoft_login' => 0,
        'enable_google_login' => 0,
        'enable_linkedin_login' => 0,
        'allow_email_password_accounts_social_login' => 1,
        'block_disposable_email_registration' => 1,
        'search_show_forums' => 1,
        'search_show_communities' => 1,
        'show_publication_card_file_type_badge' => 1,
        'communities_listing_show_participants' => 1,
        'communities_listing_max_faces' => 8,
        'publication_required_fields' => '{"tags":"1","theme":"1","title":"1","sub_theme":"1","description":"1","data_category_id":"1","associated_authors":"1"}',
        'content_disclaimer' => 'This platform provides information for research and learning purposes only and is not a substitute for professional medical advice.',
    ],
];
