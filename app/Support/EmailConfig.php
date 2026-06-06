<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class EmailConfig
{
    private static ?object $dbSettings = null;

    public static function clearCache(): void
    {
        self::$dbSettings = null;
    }

    public static function dbSettings(): ?object
    {
        if (self::$dbSettings !== null) {
            return self::$dbSettings;
        }

        if (! Schema::hasTable('setting')) {
            return null;
        }

        self::$dbSettings = \DB::table('setting')->where('status', 'active')->first()
            ?: \DB::table('setting')->first();

        return self::$dbSettings;
    }

    /**
     * ENV value wins when non-empty; otherwise use DB; then default.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        $envValue = env($envKey);
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        $db = self::dbSettings();
        $column = $dbColumn ?? self::envKeyToDbColumn($envKey);
        if ($db && $column && property_exists($db, $column)) {
            $dbValue = $db->{$column};
            if ($dbValue !== null && $dbValue !== '') {
                return $dbValue;
            }
        }

        return $default;
    }

    public static function isEnvLocked(string $envKey): bool
    {
        $value = env($envKey);

        return $value !== null && $value !== '';
    }

    public static function driver(): string
    {
        $envDriver = env('EMAIL_DRIVER');
        if ($envDriver !== null && $envDriver !== '') {
            return self::normalizeDriver($envDriver);
        }

        $mailMailer = env('MAIL_MAILER');
        if ($mailMailer !== null && $mailMailer !== '' && in_array($mailMailer, ['smtp', 'exchange'], true)) {
            return self::normalizeDriver($mailMailer);
        }

        $db = self::dbSettings();
        if ($db && ! empty($db->email_driver)) {
            return self::normalizeDriver((string) $db->email_driver);
        }

        return 'exchange';
    }

    public static function driverSource(): string
    {
        if (self::isEnvLocked('EMAIL_DRIVER')) {
            return 'env';
        }
        if (self::isEnvLocked('MAIL_MAILER') && in_array(env('MAIL_MAILER'), ['smtp', 'exchange'], true)) {
            return 'env';
        }

        $db = self::dbSettings();
        if ($db && ! empty($db->email_driver)) {
            return 'db';
        }

        return 'default';
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'email_driver')) {
            return;
        }

        $fromName = self::resolve('MAIL_FROM_NAME', 'mail_from_name')
            ?: self::resolve('MAIL_SENDER_NAME', 'mail_from_name');

        config([
            'emails.driver' => self::driver(),
            'emails.host' => self::resolve('MAIL_HOST', 'mail_host'),
            'emails.username' => self::resolve('MAIL_USERNAME', 'mail_username'),
            'emails.password' => self::resolve('MAIL_PASSWORD', 'mail_password'),
            'emails.smtp_secure' => self::resolve('MAIL_ENCRYPTION', 'mail_encryption', 'tls') ?: 'tls',
            'emails.port' => self::resolve('MAIL_PORT', 'mail_port', '587'),
            'emails.sender' => $fromName,
            'exchange-email.tenant_id' => self::resolve('EXCHANGE_TENANT_ID', 'exchange_tenant_id'),
            'exchange-email.client_id' => self::resolve('EXCHANGE_CLIENT_ID', 'exchange_client_id'),
            'exchange-email.client_secret' => self::resolve('EXCHANGE_CLIENT_SECRET', 'exchange_client_secret'),
            'exchange-email.redirect_uri' => self::resolve(
                'EXCHANGE_REDIRECT_URI',
                'exchange_redirect_uri',
                rtrim((string) env('APP_URL', ''), '/').'/auth/microsoft/callback'
            ),
            'exchange-email.scope' => self::resolve(
                'EXCHANGE_SCOPE',
                'exchange_scope',
                'https://graph.microsoft.com/.default'
            ),
            'exchange-email.auth_method' => self::resolve(
                'EXCHANGE_AUTH_METHOD',
                'exchange_auth_method',
                'client_credentials'
            ),
        ]);
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, db_value: mixed, env_locked: bool, source: string}>
     */
    public static function fieldsForAdmin(): array
    {
        $map = [
            'email_driver' => ['env_key' => 'EMAIL_DRIVER', 'db_column' => 'email_driver', 'default' => 'exchange'],
            'mail_host' => ['env_key' => 'MAIL_HOST', 'db_column' => 'mail_host', 'default' => ''],
            'mail_port' => ['env_key' => 'MAIL_PORT', 'db_column' => 'mail_port', 'default' => '587'],
            'mail_username' => ['env_key' => 'MAIL_USERNAME', 'db_column' => 'mail_username', 'default' => ''],
            'mail_password' => ['env_key' => 'MAIL_PASSWORD', 'db_column' => 'mail_password', 'default' => ''],
            'mail_encryption' => ['env_key' => 'MAIL_ENCRYPTION', 'db_column' => 'mail_encryption', 'default' => 'tls'],
            'mail_from_address' => ['env_key' => 'MAIL_FROM_ADDRESS', 'db_column' => 'mail_from_address', 'default' => ''],
            'mail_from_name' => ['env_key' => 'MAIL_FROM_NAME', 'db_column' => 'mail_from_name', 'default' => ''],
            'exchange_tenant_id' => ['env_key' => 'EXCHANGE_TENANT_ID', 'db_column' => 'exchange_tenant_id', 'default' => ''],
            'exchange_client_id' => ['env_key' => 'EXCHANGE_CLIENT_ID', 'db_column' => 'exchange_client_id', 'default' => ''],
            'exchange_client_secret' => ['env_key' => 'EXCHANGE_CLIENT_SECRET', 'db_column' => 'exchange_client_secret', 'default' => ''],
            'exchange_redirect_uri' => ['env_key' => 'EXCHANGE_REDIRECT_URI', 'db_column' => 'exchange_redirect_uri', 'default' => ''],
            'exchange_scope' => ['env_key' => 'EXCHANGE_SCOPE', 'db_column' => 'exchange_scope', 'default' => 'https://graph.microsoft.com/.default'],
            'exchange_auth_method' => ['env_key' => 'EXCHANGE_AUTH_METHOD', 'db_column' => 'exchange_auth_method', 'default' => 'client_credentials'],
        ];

        $db = self::dbSettings();
        $fields = [];

        foreach ($map as $key => $meta) {
            $dbValue = ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null;
            $envLocked = self::isEnvLocked($meta['env_key']);
            $source = $envLocked ? 'env' : (($dbValue !== null && $dbValue !== '') ? 'db' : 'default');
            $effective = self::resolve($meta['env_key'], $meta['db_column'], $meta['default']);

            if ($key === 'email_driver') {
                $effective = self::driver();
                $source = self::driverSource();
                $envLocked = $source === 'env';
            }

            $fields[$key] = [
                'env_key' => $meta['env_key'],
                'db_column' => $meta['db_column'],
                'value' => $effective,
                'db_value' => $dbValue,
                'env_locked' => $envLocked,
                'source' => $source,
            ];
        }

        return $fields;
    }

    private static function normalizeDriver(string $driver): string
    {
        return $driver === 'smtp' ? 'smtp' : 'exchange';
    }

    private static function envKeyToDbColumn(string $envKey): string
    {
        return strtolower($envKey);
    }
}
