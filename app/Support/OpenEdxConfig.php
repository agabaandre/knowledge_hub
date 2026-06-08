<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class OpenEdxConfig
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
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        return EnvFirstConfig::resolve(self::dbSettings(), $envKey, $dbColumn, $default);
    }

    public static function lmsUrl(): string
    {
        return rtrim((string) self::resolve('OPENEDX_LMS_URL', 'openedx_lms_url', ''), '/');
    }

    public static function clientId(): string
    {
        return (string) self::resolve('OPENEDX_CLIENT_ID', 'openedx_client_id', '');
    }

    public static function clientSecret(): string
    {
        return (string) self::resolve('OPENEDX_CLIENT_SECRET', 'openedx_client_secret', '');
    }

    public static function tokenUrl(): string
    {
        $configured = (string) self::resolve('OPENEDX_TOKEN_URL', 'openedx_token_url', '');
        if ($configured !== '') {
            return $configured;
        }

        $lms = self::lmsUrl();

        return $lms !== '' ? $lms.'/oauth2/access_token' : '';
    }

    public static function syncEnabled(): bool
    {
        $db = self::dbSettings();
        $envDefault = filter_var(EnvFirstConfig::envEffective('OPENEDX_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if ($db && property_exists($db, 'openedx_sync_enabled') && $db->openedx_sync_enabled !== null
            && EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'openedx_sync_enabled')) {
            return (bool) $db->openedx_sync_enabled;
        }

        return (bool) $envDefault;
    }

    public static function courseViewUrl(string $courseId): ?string
    {
        if ($courseId === '') {
            return null;
        }

        $base = self::lmsUrl();
        if ($base === '') {
            return null;
        }

        $path = str_replace('{id}', rawurlencode($courseId), (string) config('openedx.course_path', '/courses/{id}/about'));

        return $base.'/'.ltrim($path, '/');
    }

    public static function isConfigured(): bool
    {
        return self::lmsUrl() !== '' && self::clientId() !== '' && self::clientSecret() !== '';
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'openedx_lms_url')) {
            return;
        }

        config([
            'openedx.lms_url' => self::lmsUrl(),
            'openedx.client_id' => self::clientId(),
            'openedx.client_secret' => self::clientSecret(),
            'openedx.token_url' => self::tokenUrl(),
            'openedx.sync_enabled' => self::syncEnabled(),
        ]);
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, form_value: mixed}>
     */
    public static function fieldsForAdmin(): array
    {
        $db = self::dbSettings();
        $map = [
            'openedx_lms_url' => ['env_key' => 'OPENEDX_LMS_URL', 'db_column' => 'openedx_lms_url', 'default' => ''],
            'openedx_client_id' => ['env_key' => 'OPENEDX_CLIENT_ID', 'db_column' => 'openedx_client_id', 'default' => ''],
            'openedx_client_secret' => ['env_key' => 'OPENEDX_CLIENT_SECRET', 'db_column' => 'openedx_client_secret', 'default' => ''],
            'openedx_token_url' => ['env_key' => 'OPENEDX_TOKEN_URL', 'db_column' => 'openedx_token_url', 'default' => ''],
            'openedx_sync_enabled' => ['env_key' => 'OPENEDX_SYNC_ENABLED', 'db_column' => 'openedx_sync_enabled', 'default' => false],
        ];

        $fields = [];
        foreach ($map as $key => $meta) {
            if ($key === 'openedx_sync_enabled') {
                $envDefault = filter_var(EnvFirstConfig::envEffective('OPENEDX_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
                $fields[$key] = [
                    'env_key' => $meta['env_key'],
                    'db_column' => $meta['db_column'],
                    'value' => self::syncEnabled(),
                    'form_value' => self::syncEnabled(),
                    'source' => EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'openedx_sync_enabled') ? 'database' : 'env',
                    'has_db_override' => EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'openedx_sync_enabled'),
                ];

                continue;
            }

            $meta['secret'] = $key === 'openedx_client_secret';
            $fields[$key] = EnvFirstConfig::adminField($db, $meta);
        }

        return $fields;
    }
}
