<?php

return [

    /*
    | Bare-metal auto-provision of country hubs from the continental admin UI.
    | Credentials must only live in continental .env — never expose in the browser.
    */
    'enabled' => filter_var(env('FEDERATION_PROVISION_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'source_root' => env('FEDERATION_PROVISION_SOURCE_ROOT', '/var/www/khub.africacdc.org'),
    'target_parent' => env('FEDERATION_PROVISION_TARGET_PARENT', '/var/www'),
    'apache_vhost' => env('FEDERATION_PROVISION_APACHE_VHOST', '/etc/apache2/sites-available/khub.africacdc.org-le-ssl.conf'),
    'public_base_url' => rtrim((string) env('FEDERATION_PROVISION_PUBLIC_BASE_URL', env('APP_URL', 'https://khub.africacdc.org')), '/'),

    'web_user' => env('FEDERATION_PROVISION_WEB_USER', 'www-data'),
    'web_group' => env('FEDERATION_PROVISION_WEB_GROUP', 'www-data'),
    'sudo_password' => env('FEDERATION_PROVISION_SUDO_PASSWORD', ''),

    'mysql' => [
        'host' => env('FEDERATION_PROVISION_MYSQL_HOST', env('DB_HOST', '127.0.0.1')),
        'port' => (int) env('FEDERATION_PROVISION_MYSQL_PORT', env('DB_PORT', 3306)),
        'admin_user' => env('FEDERATION_PROVISION_MYSQL_ADMIN_USER', 'root'),
        'admin_password' => env('FEDERATION_PROVISION_MYSQL_ADMIN_PASSWORD', ''),
    ],

    'php_binary' => env('FEDERATION_PROVISION_PHP_BINARY', PHP_BINARY),

    'reserved_slugs' => [
        'admin', 'api', 'storage', 'login', 'install', 'federated', 'telescope',
        'horizon', 'oauth', 'passport', 'vendor', 'public', 'assets', 'css', 'js',
        'fonts', 'images', 'uploads', 'livewire', 'sanctum', 'broadcasting',
        'outbreak_dashboards', 'dbman', 'elearning', 'intranet', 'demo',
    ],

    'rsync_excludes' => [
        '.env',
        '.env.*',
        'storage/logs/*',
        'storage/framework/cache/*',
        'storage/framework/sessions/*',
        'storage/framework/views/*',
        'storage/app/installed.lock',
        'node_modules',
        '.git',
        '.idea',
        '.vscode',
    ],

];
