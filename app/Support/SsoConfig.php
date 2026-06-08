<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SsoConfig
{
    private static ?object $dbSettings = null;

    public static function clearCache(): void
    {
        self::$dbSettings = null;
        EnvFirstConfig::clearEnvFileCache();
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
     * .env is default; database stores only values that differ from .env.
     */
    public static function usesDatabaseCredentials(): bool
    {
        return self::credentialSource() === 'database';
    }

    public static function credentialSource(): string
    {
        $db = self::dbSettings();
        if (! $db) {
            return 'env';
        }

        foreach ([
            ['MICROSOFT_CLIENT_ID', 'microsoft_client_id'],
            ['MICROSOFT_CLIENT_SECRET', 'microsoft_client_secret'],
            ['GOOGLE_CLIENT_ID', 'google_client_id'],
            ['GOOGLE_CLIENT_SECRET', 'google_client_secret'],
            ['LINKEDIN_CLIENT_ID', 'linkedin_client_id'],
            ['LINKEDIN_CLIENT_SECRET', 'linkedin_client_secret'],
        ] as [$envKey, $column]) {
            if (EnvFirstConfig::hasOverrideForEffective($db, self::resolveFromEnv($envKey, ''), $column)) {
                return 'database';
            }
        }

        return 'env';
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        return EnvFirstConfig::resolveWithEnvEffective(
            self::dbSettings(),
            self::resolveFromEnv($envKey, $default),
            $dbColumn ?? strtolower($envKey)
        );
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolveFromEnv(string $envKey, $default = null)
    {
        $envValue = EnvFirstConfig::envEffective($envKey, null, self::configKeyForEnv($envKey));
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        if ($envKey === 'MICROSOFT_CLIENT_ID') {
            foreach (['EXCHANGE_CLIENT_ID'] as $exchangeKey) {
                $exchange = EnvFirstConfig::envEffective($exchangeKey);
                if ($exchange !== null && $exchange !== '') {
                    return $exchange;
                }
            }
            $configExchange = trim((string) config('exchange-email.client_id', ''));
            if ($configExchange !== '') {
                return $configExchange;
            }
        }
        if ($envKey === 'MICROSOFT_CLIENT_SECRET') {
            foreach (['EXCHANGE_CLIENT_SECRET'] as $exchangeKey) {
                $exchange = EnvFirstConfig::envEffective($exchangeKey);
                if ($exchange !== null && $exchange !== '') {
                    return $exchange;
                }
            }
            $configExchange = trim((string) config('exchange-email.client_secret', ''));
            if ($configExchange !== '') {
                return $configExchange;
            }
        }
        if ($envKey === 'MICROSOFT_TENANT_ID') {
            foreach (['EXCHANGE_TENANT_ID'] as $exchangeKey) {
                $exchange = EnvFirstConfig::envEffective($exchangeKey);
                if ($exchange !== null && $exchange !== '') {
                    return $exchange;
                }
            }
            $configExchange = trim((string) config('exchange-email.tenant_id', ''));
            if ($configExchange !== '') {
                return $configExchange;
            }
        }

        return $default;
    }

    /**
     * Clear database integration overrides so .env becomes the active source again.
     *
     * @return array{updated: int, providers: array<string, bool>, redirects: array<string, string>}
     */
    public static function syncCredentialsFromEnvToDatabase(?string $envPath = null): array
    {
        if (! Schema::hasTable('setting')) {
            return ['updated' => 0, 'providers' => [], 'redirects' => []];
        }

        $columns = [
            'microsoft_client_id', 'microsoft_client_secret', 'microsoft_redirect_uri', 'microsoft_tenant_id',
            'google_client_id', 'google_client_secret', 'google_redirect_uri',
            'linkedin_client_id', 'linkedin_client_secret', 'linkedin_redirect_uri',
        ];

        $updates = [];
        foreach ($columns as $column) {
            if (Schema::hasColumn('setting', $column)) {
                $updates[$column] = null;
            }
        }

        if (Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            $updates['sso_use_database_credentials'] = false;
        }

        $updated = 0;
        if ($updates !== []) {
            $updated = DB::table('setting')->where('status', 'active')->update($updates);
            if ($updated === 0) {
                $updated = DB::table('setting')->limit(1)->update($updates);
            }
        }

        self::clearCache();
        self::applyRuntimeConfig();

        return [
            'updated' => (int) $updated,
            'providers' => [
                'microsoft' => self::providerConfigured('microsoft'),
                'google' => self::providerConfigured('google'),
                'linkedin' => self::providerConfigured('linkedin'),
            ],
            'redirects' => [
                'microsoft' => (string) config('services.microsoft.redirect', ''),
                'google' => (string) config('services.google.redirect', ''),
                'linkedin' => (string) config('services.linkedin.redirect', ''),
            ],
        ];
    }

    private static function configKeyForEnv(string $envKey): ?string
    {
        return match ($envKey) {
            'MICROSOFT_CLIENT_ID' => 'services.microsoft.client_id',
            'MICROSOFT_CLIENT_SECRET' => 'services.microsoft.client_secret',
            'MICROSOFT_REDIRECT_URI' => 'services.microsoft.redirect',
            'MICROSOFT_TENANT_ID' => 'services.microsoft.tenant',
            'GOOGLE_CLIENT_ID' => 'services.google.client_id',
            'GOOGLE_CLIENT_SECRET' => 'services.google.client_secret',
            'GOOGLE_REDIRECT_URI' => 'services.google.redirect',
            'LINKEDIN_CLIENT_ID' => 'services.linkedin.client_id',
            'LINKEDIN_CLIENT_SECRET' => 'services.linkedin.client_secret',
            'LINKEDIN_REDIRECT_URI' => 'services.linkedin.redirect',
            default => null,
        };
    }

    public static function ensureRuntimeConfig(): void
    {
        self::applyRuntimeConfig();
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
            'microsoft' => trim((string) self::resolve('MICROSOFT_CLIENT_ID', 'microsoft_client_id', '')) !== ''
                && trim((string) self::resolve('MICROSOFT_CLIENT_SECRET', 'microsoft_client_secret', '')) !== '',
            'google' => trim((string) self::resolve('GOOGLE_CLIENT_ID', 'google_client_id', '')) !== ''
                && trim((string) self::resolve('GOOGLE_CLIENT_SECRET', 'google_client_secret', '')) !== '',
            'linkedin' => trim((string) self::resolve('LINKEDIN_CLIENT_ID', 'linkedin_client_id', '')) !== ''
                && trim((string) self::resolve('LINKEDIN_CLIENT_SECRET', 'linkedin_client_secret', '')) !== '',
            default => false,
        };
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $appUrl = rtrim((string) config('app.url', ''), '/');

        config([
            'services.microsoft.client_id' => (string) self::resolve('MICROSOFT_CLIENT_ID', 'microsoft_client_id', ''),
            'services.microsoft.client_secret' => (string) self::resolve('MICROSOFT_CLIENT_SECRET', 'microsoft_client_secret', ''),
            'services.microsoft.redirect' => (string) self::resolve(
                'MICROSOFT_REDIRECT_URI',
                'microsoft_redirect_uri',
                $appUrl.'/auth/microsoft/callback'
            ),
            'services.microsoft.tenant' => (string) self::resolve(
                'MICROSOFT_TENANT_ID',
                'microsoft_tenant_id',
                'common'
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
        $appUrl = rtrim((string) config('app.url', ''), '/');
        $map = [
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
            $envEffective = ! empty($meta['env_key'])
                ? self::resolveFromEnv($meta['env_key'], $meta['default'])
                : null;
            $field = ! empty($meta['boolean'])
                ? EnvFirstConfig::adminField($db, $meta)
                : EnvFirstConfig::adminField($db, array_merge($meta, [
                    'secret' => str_contains($key, '_secret'),
                ]), $envEffective);
            $field['boolean'] = ! empty($meta['boolean']);
            $fields[$key] = $field;
        }

        return $fields;
    }
}
