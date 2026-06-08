<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class SsoConfig
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

    public static function usesDatabaseCredentials(): bool
    {
        if (! Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            return false;
        }

        $db = self::dbSettings();
        if ($db && property_exists($db, 'sso_use_database_credentials')
            && $db->sso_use_database_credentials !== null && $db->sso_use_database_credentials !== '') {
            return filter_var($db->sso_use_database_credentials, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    public static function credentialSource(): string
    {
        return self::usesDatabaseCredentials() ? 'database' : 'env';
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        if (self::usesDatabaseCredentials()) {
            $db = self::dbSettings();
            $column = $dbColumn ?? strtolower($envKey);
            if ($db && $column && property_exists($db, $column)) {
                $dbValue = $db->{$column};
                if ($dbValue !== null && $dbValue !== '') {
                    return $dbValue;
                }
            }
        }

        return self::resolveFromEnv($envKey, $default);
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolveFromEnv(string $envKey, $default = null)
    {
        $envValue = env($envKey);
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        if ($envKey === 'MICROSOFT_CLIENT_ID' && env('EXCHANGE_CLIENT_ID')) {
            return env('EXCHANGE_CLIENT_ID');
        }
        if ($envKey === 'MICROSOFT_CLIENT_SECRET' && env('EXCHANGE_CLIENT_SECRET')) {
            return env('EXCHANGE_CLIENT_SECRET');
        }
        if ($envKey === 'MICROSOFT_TENANT_ID' && env('EXCHANGE_TENANT_ID')) {
            return env('EXCHANGE_TENANT_ID');
        }

        return $default;
    }

    public static function microsoftEnabled(): bool
    {
        return self::providerLoginEnabled('microsoft');
    }

    public static function googleEnabled(): bool
    {
        return self::providerLoginEnabled('google');
    }

    public static function linkedinEnabled(): bool
    {
        return self::providerLoginEnabled('linkedin');
    }

    public static function providerLoginEnabled(string $provider): bool
    {
        $column = match ($provider) {
            'microsoft' => 'enable_microsoft_login',
            'google' => 'enable_google_login',
            'linkedin' => 'enable_linkedin_login',
            default => null,
        };

        if (! $column) {
            return false;
        }

        $db = self::dbSettings();
        if ($db && property_exists($db, $column) && $db->{$column} !== null && $db->{$column} !== '') {
            return filter_var($db->{$column}, FILTER_VALIDATE_BOOLEAN);
        }

        return true;
    }

    public static function providerConfigured(string $provider): bool
    {
        return match ($provider) {
            'microsoft' => (string) self::resolve('MICROSOFT_CLIENT_ID', 'microsoft_client_id', '') !== ''
                && (string) self::resolve('MICROSOFT_CLIENT_SECRET', 'microsoft_client_secret', '') !== '',
            'google' => (string) self::resolve('GOOGLE_CLIENT_ID', 'google_client_id', '') !== ''
                && (string) self::resolve('GOOGLE_CLIENT_SECRET', 'google_client_secret', '') !== '',
            'linkedin' => (string) self::resolve('LINKEDIN_CLIENT_ID', 'linkedin_client_id', '') !== ''
                && (string) self::resolve('LINKEDIN_CLIENT_SECRET', 'linkedin_client_secret', '') !== '',
            default => false,
        };
    }

    public static function applyRuntimeConfig(): void
    {
        $appUrl = rtrim((string) config('app.url', ''), '/');

        config([
            'services.microsoft.client_id' => (string) self::resolve('MICROSOFT_CLIENT_ID', 'microsoft_client_id', env('EXCHANGE_CLIENT_ID', '')),
            'services.microsoft.client_secret' => (string) self::resolve('MICROSOFT_CLIENT_SECRET', 'microsoft_client_secret', env('EXCHANGE_CLIENT_SECRET', '')),
            'services.microsoft.redirect' => (string) self::resolve(
                'MICROSOFT_REDIRECT_URI',
                'microsoft_redirect_uri',
                $appUrl.'/auth/microsoft/callback'
            ),
            'services.microsoft.tenant' => (string) self::resolve(
                'MICROSOFT_TENANT_ID',
                'microsoft_tenant_id',
                env('EXCHANGE_TENANT_ID', 'common')
            ),
            'services.google.client_id' => (string) self::resolve('GOOGLE_CLIENT_ID', 'google_client_id', ''),
            'services.google.client_secret' => (string) self::resolve('GOOGLE_CLIENT_SECRET', 'google_client_secret', ''),
            'services.google.redirect' => (string) self::resolve(
                'GOOGLE_REDIRECT_URI',
                'google_redirect_uri',
                $appUrl.'/auth/google/callback'
            ),
            'services.linkedin.client_id' => (string) self::resolve('LINKEDIN_CLIENT_ID', 'linkedin_client_id', ''),
            'services.linkedin.client_secret' => (string) self::resolve('LINKEDIN_CLIENT_SECRET', 'linkedin_client_secret', ''),
            'services.linkedin.redirect' => (string) self::resolve(
                'LINKEDIN_REDIRECT_URI',
                'linkedin_redirect_uri',
                $appUrl.'/auth/linkedin/callback'
            ),
            'services.linkedin-openid.client_id' => (string) self::resolve('LINKEDIN_CLIENT_ID', 'linkedin_client_id', ''),
            'services.linkedin-openid.client_secret' => (string) self::resolve('LINKEDIN_CLIENT_SECRET', 'linkedin_client_secret', ''),
            'services.linkedin-openid.redirect' => (string) self::resolve(
                'LINKEDIN_REDIRECT_URI',
                'linkedin_redirect_uri',
                $appUrl.'/auth/linkedin/callback'
            ),
        ]);
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, form_value: mixed, source: string}>
     */
    public static function fieldsForAdmin(): array
    {
        $db = self::dbSettings();
        $usesDb = self::usesDatabaseCredentials();
        $appUrl = rtrim((string) config('app.url', ''), '/');
        $map = [
            'sso_use_database_credentials' => ['env_key' => '', 'db_column' => 'sso_use_database_credentials', 'default' => false, 'boolean' => true],
            'microsoft_client_id' => ['env_key' => 'MICROSOFT_CLIENT_ID', 'db_column' => 'microsoft_client_id', 'default' => ''],
            'microsoft_client_secret' => ['env_key' => 'MICROSOFT_CLIENT_SECRET', 'db_column' => 'microsoft_client_secret', 'default' => ''],
            'microsoft_redirect_uri' => ['env_key' => 'MICROSOFT_REDIRECT_URI', 'db_column' => 'microsoft_redirect_uri', 'default' => $appUrl.'/auth/microsoft/callback'],
            'microsoft_tenant_id' => ['env_key' => 'MICROSOFT_TENANT_ID', 'db_column' => 'microsoft_tenant_id', 'default' => 'common'],
            'google_client_id' => ['env_key' => 'GOOGLE_CLIENT_ID', 'db_column' => 'google_client_id', 'default' => ''],
            'google_client_secret' => ['env_key' => 'GOOGLE_CLIENT_SECRET', 'db_column' => 'google_client_secret', 'default' => ''],
            'google_redirect_uri' => ['env_key' => 'GOOGLE_REDIRECT_URI', 'db_column' => 'google_redirect_uri', 'default' => $appUrl.'/auth/google/callback'],
            'linkedin_client_id' => ['env_key' => 'LINKEDIN_CLIENT_ID', 'db_column' => 'linkedin_client_id', 'default' => ''],
            'linkedin_client_secret' => ['env_key' => 'LINKEDIN_CLIENT_SECRET', 'db_column' => 'linkedin_client_secret', 'default' => ''],
            'linkedin_redirect_uri' => ['env_key' => 'LINKEDIN_REDIRECT_URI', 'db_column' => 'linkedin_redirect_uri', 'default' => $appUrl.'/auth/linkedin/callback'],
            'enable_microsoft_login' => ['env_key' => '', 'db_column' => 'enable_microsoft_login', 'default' => true, 'boolean' => true],
            'enable_google_login' => ['env_key' => '', 'db_column' => 'enable_google_login', 'default' => true, 'boolean' => true],
            'enable_linkedin_login' => ['env_key' => '', 'db_column' => 'enable_linkedin_login', 'default' => true, 'boolean' => true],
        ];

        $fields = [];
        foreach ($map as $key => $meta) {
            $dbValue = ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null;

            if (! empty($meta['boolean'])) {
                $hasDbValue = $dbValue !== null && $dbValue !== '';
                $formValue = $hasDbValue ? filter_var($dbValue, FILTER_VALIDATE_BOOLEAN) : (bool) $meta['default'];
                $value = $formValue;
                $source = 'database';
            } else {
                $envEffective = ! empty($meta['env_key'])
                    ? self::resolveFromEnv($meta['env_key'], $meta['default'])
                    : $meta['default'];
                $hasDbValue = $dbValue !== null && $dbValue !== '';
                $value = $usesDb && $hasDbValue ? $dbValue : $envEffective;
                $formValue = $hasDbValue ? $dbValue : $envEffective;
                $source = ($usesDb && $hasDbValue) ? 'database' : 'env';
            }

            if (str_contains($key, '_secret') && $hasDbValue && $formValue !== '') {
                $formValue = '';
            }

            $fields[$key] = [
                'env_key' => $meta['env_key'],
                'db_column' => $meta['db_column'],
                'value' => $value,
                'form_value' => $formValue,
                'boolean' => ! empty($meta['boolean']),
                'source' => $source,
            ];
        }

        return $fields;
    }
}
