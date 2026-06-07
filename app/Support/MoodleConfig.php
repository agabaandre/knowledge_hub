<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class MoodleConfig
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

    public static function apiUrl(): string
    {
        return (string) self::resolve('MOODLE_API_URL', 'moodle_api_url', '');
    }

    public static function apiToken(): string
    {
        return (string) self::resolve('MOODLE_API_TOKEN', 'moodle_api_token', '');
    }

    public static function baseUrl(): string
    {
        $url = (string) self::resolve('MOODLE_URL', 'moodle_base_url', '');

        return rtrim($url, '/');
    }

    public static function syncEnabled(): bool
    {
        $db = self::dbSettings();
        if ($db && property_exists($db, 'moodle_sync_enabled') && $db->moodle_sync_enabled !== null) {
            return (bool) $db->moodle_sync_enabled;
        }

        $env = env('MOODLE_SYNC_ENABLED');
        if ($env !== null && $env !== '') {
            return filter_var($env, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) config('moodle.sync_enabled', true);
    }

    public static function defaultCourseImage(): string
    {
        return (string) config('learning.default_course_image', config('moodle.default_course_image'));
    }

    public static function courseViewUrl(int $moodleCourseId): ?string
    {
        if ($moodleCourseId <= 0) {
            return null;
        }

        $base = self::baseUrl();
        if ($base === '') {
            return null;
        }

        return $base.'/course/view.php?id='.$moodleCourseId;
    }

    public static function isConfigured(): bool
    {
        return self::apiUrl() !== '' && self::apiToken() !== '' && self::baseUrl() !== '';
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'moodle_api_url')) {
            return;
        }

        config([
            'moodle.api_url' => self::apiUrl(),
            'moodle.api_token' => self::apiToken(),
            'moodle.base_url' => self::baseUrl(),
            'moodle.sync_enabled' => self::syncEnabled(),
        ]);
    }

    /**
     * @return array<string, array{env_key: string, db_column: string, value: mixed, form_value: mixed}>
     */
    public static function fieldsForAdmin(): array
    {
        $db = self::dbSettings();
        $map = [
            'moodle_api_url' => ['env_key' => 'MOODLE_API_URL', 'db_column' => 'moodle_api_url', 'default' => ''],
            'moodle_api_token' => ['env_key' => 'MOODLE_API_TOKEN', 'db_column' => 'moodle_api_token', 'default' => ''],
            'moodle_base_url' => ['env_key' => 'MOODLE_URL', 'db_column' => 'moodle_base_url', 'default' => ''],
            'moodle_sync_enabled' => ['env_key' => 'MOODLE_SYNC_ENABLED', 'db_column' => 'moodle_sync_enabled', 'default' => true],
        ];

        $fields = [];
        foreach ($map as $key => $meta) {
            $dbValue = ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null;
            $effective = self::resolve($meta['env_key'], $meta['db_column'], $meta['default']);
            $hasDbValue = $dbValue !== null && $dbValue !== '';

            $fields[$key] = [
                'env_key' => $meta['env_key'],
                'db_column' => $meta['db_column'],
                'value' => $effective,
                'form_value' => $hasDbValue ? $dbValue : $effective,
            ];
        }

        return $fields;
    }
}
