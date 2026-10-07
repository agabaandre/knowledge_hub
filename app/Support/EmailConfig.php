<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class EmailConfig
{
    private static ?object $dbSettings = null;

    public static function clearCache(): void
    {
        self::$dbSettings = null;
        EnvFirstConfig::clearEnvFileCache();
    }

    /**
     * Factory default for Africa CDC HTTP mail (from config/emails.php only).
     */
    public static function httpBaseUrlDefault(): string
    {
        return rtrim((string) config('emails.http.default_base_url', config('emails.http.base_url', '')), '/');
    }

    /**
     * Effective HTTP mail API base URL (env / DB / config default).
     */
    public static function httpBaseUrl(): string
    {
        $resolved = self::resolve('MAIL_HTTP_BASE_URL', 'mail_http_base_url', self::httpBaseUrlDefault());

        return rtrim((string) ($resolved ?: self::httpBaseUrlDefault()), '/');
    }

    public static function httpDocsUrl(): string
    {
        return (string) config('emails.http.docs_url', '');
    }

    /**
     * Flat form/env field => JSON key inside mail_api_config.
     *
     * @return array<string, string>
     */
    public static function apiConfigFieldMap(): array
    {
        return [
            'mail_api_key' => 'key',
            'mail_api_secret' => 'secret',
            'mail_api_domain' => 'domain',
            'mail_api_region' => 'region',
            'mail_api_base_url' => 'base_url',
            'mail_api_message_stream' => 'message_stream',
        ];
    }

    public static function hasApiConfigStorage(): bool
    {
        return Schema::hasTable('setting')
            && (Schema::hasColumn('setting', 'mail_api_config')
                || Schema::hasColumn('setting', 'mail_api_key'));
    }

    /**
     * @return array<string, string|null>
     */
    public static function decodeApiConfig(?string $json): array
    {
        $decoded = json_decode((string) $json, true);
        if (! is_array($decoded)) {
            return [];
        }

        $out = [];
        foreach (self::apiConfigFieldMap() as $field => $jsonKey) {
            $value = $decoded[$jsonKey] ?? null;
            $out[$field] = ($value === null || $value === '') ? null : (string) $value;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $flat
     */
    public static function encodeApiConfig(array $flat): ?string
    {
        $cfg = [];
        foreach (self::apiConfigFieldMap() as $field => $jsonKey) {
            $value = $flat[$field] ?? null;
            if ($value !== null && $value !== '') {
                $cfg[$jsonKey] = (string) $value;
            }
        }

        return $cfg === [] ? null : json_encode($cfg, JSON_UNESCAPED_SLASHES);
    }

    public static function hydrateApiFields(?object $row): ?object
    {
        if (! $row) {
            return $row;
        }

        if (property_exists($row, 'mail_api_config') || Schema::hasColumn('setting', 'mail_api_config')) {
            $flat = self::decodeApiConfig(isset($row->mail_api_config) ? (string) $row->mail_api_config : null);
            foreach (self::apiConfigFieldMap() as $field => $_jsonKey) {
                if (! property_exists($row, $field) || $row->{$field} === null || $row->{$field} === '') {
                    $row->{$field} = $flat[$field] ?? null;
                }
            }
        }

        return $row;
    }

    public static function dbSettings(): ?object
    {
        if (self::$dbSettings !== null) {
            return self::$dbSettings;
        }

        if (! Schema::hasTable('setting')) {
            return null;
        }

        self::$dbSettings = self::hydrateApiFields(
            \DB::table('setting')->where('status', 'active')->first()
                ?: \DB::table('setting')->first()
        );

        return self::$dbSettings;
    }

    /**
     * .env is default; database stores only values that differ from .env.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        return EnvFirstConfig::resolve(
            self::dbSettings(),
            $envKey,
            $dbColumn ?? self::envKeyToDbColumn($envKey),
            $default
        );
    }

    public static function isEnvLocked(string $envKey): bool
    {
        return false;
    }

    public static function driver(): string
    {
        $db = self::dbSettings();
        $envDriver = self::envDriverEffective();

        if ($db && ! empty($db->email_driver)
            && EnvFirstConfig::hasOverrideForEffective($db, $envDriver, 'email_driver')) {
            return self::normalizeDriver((string) $db->email_driver);
        }

        return self::normalizeDriver($envDriver);
    }

    public static function envDriverFromEnv(): string
    {
        return self::envDriverEffective();
    }

    public static function driverSource(): string
    {
        $db = self::dbSettings();
        $envDriver = self::envDriverEffective();

        if ($db && ! empty($db->email_driver)
            && EnvFirstConfig::hasOverrideForEffective($db, $envDriver, 'email_driver')) {
            return 'db';
        }

        if (EnvFirstConfig::envEffective('EMAIL_DRIVER') !== null && EnvFirstConfig::envEffective('EMAIL_DRIVER') !== '') {
            return 'env';
        }

        if (EnvFirstConfig::envEffective('MAIL_MAILER') !== null && EnvFirstConfig::envEffective('MAIL_MAILER') !== ''
            && EmailDrivers::isSupported((string) EnvFirstConfig::envEffective('MAIL_MAILER'))) {
            return 'env';
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
            'emails.from_address' => self::resolve('MAIL_FROM_ADDRESS', 'mail_from_address'),
            'exchange-email.tenant_id' => self::resolve('EXCHANGE_TENANT_ID', 'exchange_tenant_id'),
            'exchange-email.client_id' => self::resolve('EXCHANGE_CLIENT_ID', 'exchange_client_id'),
            'exchange-email.client_secret' => self::resolve('EXCHANGE_CLIENT_SECRET', 'exchange_client_secret'),
            'exchange-email.redirect_uri' => self::resolve(
                'EXCHANGE_REDIRECT_URI',
                'exchange_redirect_uri',
                rtrim((string) config('app.url', ''), '/').'/auth/microsoft/callback'
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

        if (Schema::hasColumn('setting', 'mail_http_client_id')) {
            config([
                'emails.http.base_url' => self::httpBaseUrl(),
                'emails.http.client_id' => self::resolve('MAIL_HTTP_CLIENT_ID', 'mail_http_client_id'),
                'emails.http.client_secret' => self::resolve('MAIL_HTTP_CLIENT_SECRET', 'mail_http_client_secret'),
            ]);
        } else {
            config([
                'emails.http.base_url' => self::httpBaseUrlDefault() ?: env('MAIL_HTTP_BASE_URL'),
                'emails.http.client_id' => env('MAIL_HTTP_CLIENT_ID'),
                'emails.http.client_secret' => env('MAIL_HTTP_CLIENT_SECRET'),
            ]);
        }

        if (self::hasApiConfigStorage()) {
            config([
                'emails.api.key' => self::resolve('MAIL_API_KEY', 'mail_api_key'),
                'emails.api.secret' => self::resolve('MAIL_API_SECRET', 'mail_api_secret'),
                'emails.api.domain' => self::resolve('MAIL_API_DOMAIN', 'mail_api_domain'),
                'emails.api.region' => self::resolve('MAIL_API_REGION', 'mail_api_region', 'us') ?: 'us',
                'emails.api.base_url' => self::resolve('MAIL_API_BASE_URL', 'mail_api_base_url'),
                'emails.api.message_stream' => self::resolve('MAIL_API_MESSAGE_STREAM', 'mail_api_message_stream', 'outbound') ?: 'outbound',
            ]);
        } else {
            config([
                'emails.api.key' => env('MAIL_API_KEY'),
                'emails.api.secret' => env('MAIL_API_SECRET'),
                'emails.api.domain' => env('MAIL_API_DOMAIN'),
                'emails.api.region' => env('MAIL_API_REGION', 'us'),
                'emails.api.base_url' => env('MAIL_API_BASE_URL'),
                'emails.api.message_stream' => env('MAIL_API_MESSAGE_STREAM', 'outbound'),
            ]);
        }

        // Zoho SMTP defaults when host/port left blank
        if (self::driver() === 'zoho') {
            $defaults = EmailDrivers::smtpDefaults('zoho');
            if (trim((string) config('emails.host')) === '') {
                config(['emails.host' => $defaults['host']]);
            }
            if (trim((string) config('emails.port')) === '') {
                config(['emails.port' => $defaults['port']]);
            }
            if (trim((string) config('emails.smtp_secure')) === '') {
                config(['emails.smtp_secure' => $defaults['encryption']]);
            }
        }
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, db_value: mixed, form_value: mixed, env_locked: bool, has_env_override: bool, source: string}>
     */
    public static function fieldsForAdmin(): array
    {
        $map = [
            'email_driver' => ['env_key' => 'EMAIL_DRIVER', 'db_column' => 'email_driver', 'default' => 'exchange'],
            'mail_host' => ['env_key' => 'MAIL_HOST', 'db_column' => 'mail_host', 'default' => ''],
            'mail_port' => ['env_key' => 'MAIL_PORT', 'db_column' => 'mail_port', 'default' => '587'],
            'mail_username' => ['env_key' => 'MAIL_USERNAME', 'db_column' => 'mail_username', 'default' => ''],
            'mail_password' => ['env_key' => 'MAIL_PASSWORD', 'db_column' => 'mail_password', 'default' => '', 'secret' => true],
            'mail_encryption' => ['env_key' => 'MAIL_ENCRYPTION', 'db_column' => 'mail_encryption', 'default' => 'tls'],
            'mail_from_address' => ['env_key' => 'MAIL_FROM_ADDRESS', 'db_column' => 'mail_from_address', 'default' => ''],
            'mail_from_name' => ['env_key' => 'MAIL_FROM_NAME', 'db_column' => 'mail_from_name', 'default' => ''],
            'exchange_tenant_id' => ['env_key' => 'EXCHANGE_TENANT_ID', 'db_column' => 'exchange_tenant_id', 'default' => ''],
            'exchange_client_id' => ['env_key' => 'EXCHANGE_CLIENT_ID', 'db_column' => 'exchange_client_id', 'default' => ''],
            'exchange_client_secret' => ['env_key' => 'EXCHANGE_CLIENT_SECRET', 'db_column' => 'exchange_client_secret', 'default' => '', 'secret' => true],
            'exchange_redirect_uri' => ['env_key' => 'EXCHANGE_REDIRECT_URI', 'db_column' => 'exchange_redirect_uri', 'default' => ''],
            'exchange_scope' => ['env_key' => 'EXCHANGE_SCOPE', 'db_column' => 'exchange_scope', 'default' => 'https://graph.microsoft.com/.default'],
            'exchange_auth_method' => ['env_key' => 'EXCHANGE_AUTH_METHOD', 'db_column' => 'exchange_auth_method', 'default' => 'client_credentials'],
        ];

        if (Schema::hasColumn('setting', 'mail_http_client_id')) {
            $map['mail_http_base_url'] = ['env_key' => 'MAIL_HTTP_BASE_URL', 'db_column' => 'mail_http_base_url', 'default' => self::httpBaseUrlDefault()];
            $map['mail_http_client_id'] = ['env_key' => 'MAIL_HTTP_CLIENT_ID', 'db_column' => 'mail_http_client_id', 'default' => ''];
            $map['mail_http_client_secret'] = ['env_key' => 'MAIL_HTTP_CLIENT_SECRET', 'db_column' => 'mail_http_client_secret', 'default' => '', 'secret' => true];
        }

        if (self::hasApiConfigStorage()) {
            $map['mail_api_key'] = ['env_key' => 'MAIL_API_KEY', 'db_column' => 'mail_api_key', 'default' => '', 'secret' => true];
            $map['mail_api_secret'] = ['env_key' => 'MAIL_API_SECRET', 'db_column' => 'mail_api_secret', 'default' => '', 'secret' => true];
            $map['mail_api_domain'] = ['env_key' => 'MAIL_API_DOMAIN', 'db_column' => 'mail_api_domain', 'default' => ''];
            $map['mail_api_region'] = ['env_key' => 'MAIL_API_REGION', 'db_column' => 'mail_api_region', 'default' => 'us'];
            $map['mail_api_base_url'] = ['env_key' => 'MAIL_API_BASE_URL', 'db_column' => 'mail_api_base_url', 'default' => ''];
            $map['mail_api_message_stream'] = ['env_key' => 'MAIL_API_MESSAGE_STREAM', 'db_column' => 'mail_api_message_stream', 'default' => 'outbound'];
        }

        $db = self::dbSettings();
        $fields = [];

        foreach ($map as $key => $meta) {
            if ($key === 'email_driver') {
                $envDriver = self::envDriverEffective();
                $effective = self::driver();
                $hasOverride = EnvFirstConfig::hasOverrideForEffective($db, $envDriver, 'email_driver');
                $fields[$key] = [
                    'env_key' => $meta['env_key'],
                    'db_column' => $meta['db_column'],
                    'value' => $effective,
                    'db_value' => ($db && ! empty($db->email_driver)) ? $db->email_driver : null,
                    'form_value' => $effective,
                    'env_locked' => false,
                    'has_env_override' => ! $hasOverride && $envDriver !== 'exchange',
                    'source' => self::driverSource(),
                ];

                continue;
            }

            $field = EnvFirstConfig::adminField($db, $meta);
            $fields[$key] = array_merge($field, [
                'db_value' => ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null,
                'env_locked' => false,
                'has_env_override' => ($field['source'] ?? '') === 'env' && ($field['value'] ?? '') !== '',
            ]);
        }

        return $fields;
    }

    private static function envDriverEffective(): string
    {
        $envDriver = EnvFirstConfig::envEffective('EMAIL_DRIVER');
        if ($envDriver !== null && $envDriver !== '') {
            return self::normalizeDriver((string) $envDriver);
        }

        $mailMailer = EnvFirstConfig::envEffective('MAIL_MAILER');
        if ($mailMailer !== null && $mailMailer !== '' && EmailDrivers::isSupported((string) $mailMailer)) {
            return self::normalizeDriver((string) $mailMailer);
        }

        return 'exchange';
    }

    private static function normalizeDriver(string $driver): string
    {
        return EmailDrivers::normalize($driver);
    }

    private static function envKeyToDbColumn(string $envKey): string
    {
        return strtolower($envKey);
    }
}
