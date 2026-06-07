<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class OpenEdxConfig
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
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        $db = self::dbSettings();
        $column = $dbColumn ?? strtolower($envKey);
        if ($db && $column && property_exists($db, $column)) {
            $dbValue = $db->{$column};
            if ($dbValue !== null && $dbValue !== '') {
                return $dbValue;
            }
        }

        $envValue = env($envKey);
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        return $default;
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
        if ($db && property_exists($db, 'openedx_sync_enabled') && $db->openedx_sync_enabled !== null) {
            return (bool) $db->openedx_sync_enabled;
        }

        $env = env('OPENEDX_SYNC_ENABLED');
        if ($env !== null && $env !== '') {
            return filter_var($env, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) config('openedx.sync_enabled', false);
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
            $dbValue = ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null;
            $effective = self::resolve($meta['env_key'], $meta['db_column'], $meta['default']);
            $isBoolean = str_ends_with($key, '_sync_enabled');
            if ($isBoolean) {
                $hasDbValue = $dbValue !== null;
                $formValue = filter_var($hasDbValue ? $dbValue : $effective, FILTER_VALIDATE_BOOLEAN);
                $value = filter_var($effective, FILTER_VALIDATE_BOOLEAN);
            } else {
                $hasDbValue = $dbValue !== null && $dbValue !== '';
                $formValue = $hasDbValue ? $dbValue : $effective;
                $value = $effective;
            }

            $fields[$key] = [
                'env_key' => $meta['env_key'],
                'db_column' => $meta['db_column'],
                'value' => $value,
                'form_value' => $formValue,
            ];
        }

        return $fields;
    }
}
