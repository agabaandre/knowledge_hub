<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class FrappeConfig
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

    public static function baseUrl(): string
    {
        return rtrim((string) self::resolve('FRAPPE_BASE_URL', 'frappe_base_url', ''), '/');
    }

    public static function apiKey(): string
    {
        return (string) self::resolve('FRAPPE_API_KEY', 'frappe_api_key', '');
    }

    public static function apiSecret(): string
    {
        return (string) self::resolve('FRAPPE_API_SECRET', 'frappe_api_secret', '');
    }

    public static function courseDoctype(): string
    {
        return (string) self::resolve('FRAPPE_COURSE_DOCTYPE', 'frappe_course_doctype', 'LMS Course');
    }

    public static function syncEnabled(): bool
    {
        $db = self::dbSettings();
        $envDefault = filter_var(EnvFirstConfig::envEffective('FRAPPE_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if ($db && property_exists($db, 'frappe_sync_enabled') && $db->frappe_sync_enabled !== null
            && EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'frappe_sync_enabled')) {
            return (bool) $db->frappe_sync_enabled;
        }

        return (bool) $envDefault;
    }

    public static function courseViewUrl(string $courseName): ?string
    {
        if ($courseName === '') {
            return null;
        }

        $base = self::baseUrl();
        if ($base === '') {
            return null;
        }

        $path = str_replace('{id}', rawurlencode($courseName), (string) config('frappe.course_path', '/lms/courses/{id}'));

        return $base.'/'.ltrim($path, '/');
    }

    public static function isConfigured(): bool
    {
        return self::baseUrl() !== '' && self::apiKey() !== '' && self::apiSecret() !== '';
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'frappe_base_url')) {
            return;
        }

        config([
            'frappe.base_url' => self::baseUrl(),
            'frappe.api_key' => self::apiKey(),
            'frappe.api_secret' => self::apiSecret(),
            'frappe.course_doctype' => self::courseDoctype(),
            'frappe.sync_enabled' => self::syncEnabled(),
        ]);
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, form_value: mixed}>
     */
    public static function fieldsForAdmin(): array
    {
        $db = self::dbSettings();
        $map = [
            'frappe_base_url' => ['env_key' => 'FRAPPE_BASE_URL', 'db_column' => 'frappe_base_url', 'default' => ''],
            'frappe_api_key' => ['env_key' => 'FRAPPE_API_KEY', 'db_column' => 'frappe_api_key', 'default' => ''],
            'frappe_api_secret' => ['env_key' => 'FRAPPE_API_SECRET', 'db_column' => 'frappe_api_secret', 'default' => ''],
            'frappe_course_doctype' => ['env_key' => 'FRAPPE_COURSE_DOCTYPE', 'db_column' => 'frappe_course_doctype', 'default' => 'LMS Course'],
            'frappe_sync_enabled' => ['env_key' => 'FRAPPE_SYNC_ENABLED', 'db_column' => 'frappe_sync_enabled', 'default' => false],
        ];

        $fields = [];
        foreach ($map as $key => $meta) {
            if ($key === 'frappe_sync_enabled') {
                $envDefault = filter_var(EnvFirstConfig::envEffective('FRAPPE_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
                $fields[$key] = [
                    'env_key' => $meta['env_key'],
                    'db_column' => $meta['db_column'],
                    'value' => self::syncEnabled(),
                    'form_value' => self::syncEnabled(),
                    'source' => EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'frappe_sync_enabled') ? 'database' : 'env',
                    'has_db_override' => EnvFirstConfig::hasOverrideForEffective($db, $envDefault, 'frappe_sync_enabled'),
                ];

                continue;
            }

            $meta['secret'] = $key === 'frappe_api_secret';
            $fields[$key] = EnvFirstConfig::adminField($db, $meta);
        }

        return $fields;
    }
}
