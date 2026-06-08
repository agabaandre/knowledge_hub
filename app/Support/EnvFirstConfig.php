<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Env-first integration settings: .env is the default source; database stores only explicit admin overrides.
 */
class EnvFirstConfig
{
    /** @var array<string, string>|null */
    private static ?array $envFileCache = null;

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(?object $db, string $envKey, ?string $dbColumn, $default = null, ?string $configKey = null)
    {
        return self::resolveWithEnvEffective(
            $db,
            self::envEffective($envKey, $default, $configKey),
            $dbColumn
        );
    }

    /**
     * @param  mixed  $envEffective
     * @return mixed
     */
    public static function resolveWithEnvEffective(?object $db, $envEffective, ?string $dbColumn)
    {
        if ($db && $dbColumn && property_exists($db, $dbColumn)) {
            $dbValue = $db->{$dbColumn};
            if ($dbValue !== null && $dbValue !== '' && self::valuesDiffer($dbValue, $envEffective)) {
                return $dbValue;
            }
        }

        return $envEffective;
    }

    /**
     * @param  mixed  $envEffective
     */
    public static function hasOverrideForEffective(?object $db, $envEffective, ?string $dbColumn): bool
    {
        if (! $db || ! $dbColumn || ! property_exists($db, $dbColumn)) {
            return false;
        }

        $dbValue = $db->{$dbColumn};
        if ($dbValue === null || $dbValue === '') {
            return false;
        }

        return self::valuesDiffer($dbValue, $envEffective);
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function envEffective(string $envKey, $default = null, ?string $configKey = null)
    {
        $envValue = self::envValue($envKey);
        if ($envValue !== '') {
            return $envValue;
        }

        if ($configKey !== null) {
            $configValue = config($configKey);
            if ($configValue !== null && $configValue !== '') {
                return $configValue;
            }
        }

        return $default;
    }

    public static function hasDbOverride(?object $db, string $envKey, ?string $dbColumn, $default = null, ?string $configKey = null): bool
    {
        $column = $dbColumn ?? strtolower($envKey);
        if (! $db || ! $column || ! property_exists($db, $column)) {
            return false;
        }

        $dbValue = $db->{$column};
        if ($dbValue === null || $dbValue === '') {
            return false;
        }

        return self::valuesDiffer($dbValue, self::envEffective($envKey, $default, $configKey));
    }

    /**
     * @param  array{env_key: string, db_column: string, default?: mixed, config_key?: string|null, secret?: bool, boolean?: bool}  $meta
     * @param  mixed|null  $envEffectiveOverride
     * @return array{env_key: string, db_column: string, value: mixed, form_value: mixed, source: string, has_db_override: bool}
     */
    public static function adminField(?object $db, array $meta, $envEffectiveOverride = null): array
    {
        $envKey = $meta['env_key'];
        $dbColumn = $meta['db_column'];
        $default = $meta['default'] ?? null;
        $configKey = $meta['config_key'] ?? null;
        $isSecret = ! empty($meta['secret']);
        $isBoolean = ! empty($meta['boolean']);

        if ($isBoolean) {
            $dbValue = ($db && property_exists($db, $dbColumn)) ? $db->{$dbColumn} : null;
            $hasDbValue = $dbValue !== null && $dbValue !== '';
            $effective = $hasDbValue
                ? filter_var($dbValue, FILTER_VALIDATE_BOOLEAN)
                : (bool) ($default ?? false);

            return [
                'env_key' => $envKey,
                'db_column' => $dbColumn,
                'value' => $effective,
                'form_value' => $effective,
                'source' => 'database',
                'has_db_override' => $hasDbValue,
            ];
        }

        $envEffective = $envEffectiveOverride ?? self::envEffective($envKey, $default, $configKey);
        $effective = self::resolveWithEnvEffective($db, $envEffective, $dbColumn);
        $hasOverride = self::hasOverrideForEffective($db, $envEffective, $dbColumn);

        if ($isSecret) {
            return [
                'env_key' => $envKey,
                'db_column' => $dbColumn,
                'value' => ($effective !== null && $effective !== '') ? $effective : '',
                'form_value' => '',
                'source' => $hasOverride ? 'database' : 'env',
                'has_db_override' => $hasOverride,
            ];
        }

        return [
            'env_key' => $envKey,
            'db_column' => $dbColumn,
            'value' => $effective,
            'form_value' => $effective,
            'source' => $hasOverride ? 'database' : 'env',
            'has_db_override' => $hasOverride,
        ];
    }

    /**
     * Persist a submitted field only when it differs from the current .env effective value.
     *
     * @param  mixed  $default
     */
    public static function applySubmittedOverride(
        object $settings,
        Request $request,
        string $requestKey,
        string $envKey,
        string $dbColumn,
        bool $isSecret = false,
        $default = null,
        ?string $configKey = null
    ): void {
        self::applySubmittedOverrideWithEnvEffective(
            $settings,
            $request,
            $requestKey,
            $dbColumn,
            self::envEffective($envKey, $default ?? '', $configKey),
            $isSecret
        );
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function applySubmittedOverrideWithEnvEffective(
        object $settings,
        Request $request,
        string $requestKey,
        string $dbColumn,
        $envEffective,
        bool $isSecret = false
    ): void {
        if (! Schema::hasColumn('setting', $dbColumn)) {
            return;
        }

        if ($isSecret) {
            if (! $request->filled($requestKey)) {
                return;
            }

            $submitted = (string) $request->input($requestKey);
            $settings->{$dbColumn} = self::valuesDiffer($submitted, $envEffective) ? $submitted : null;

            return;
        }

        if (! $request->has($requestKey)) {
            return;
        }

        $submitted = trim((string) $request->input($requestKey, ''));
        $settings->{$dbColumn} = self::valuesDiffer($submitted, trim((string) $envEffective)) ? $submitted : null;
    }

    /**
     * @return array<string, string>
     */
    public static function readEnvFile(?string $path = null): array
    {
        $path ??= base_path('.env');

        if (self::$envFileCache !== null && $path === base_path('.env')) {
            return self::$envFileCache;
        }

        $values = [];
        if (! is_readable($path)) {
            return $values;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"'))
                || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }

            $values[$key] = $value;
        }

        if ($path === base_path('.env')) {
            self::$envFileCache = $values;
        }

        return $values;
    }

    public static function clearEnvFileCache(): void
    {
        self::$envFileCache = null;
    }

    private static function envValue(string $key): string
    {
        $value = env($key);
        if ($value !== null && $value !== '') {
            return trim((string) $value);
        }

        $getenv = getenv($key);
        if ($getenv !== false && $getenv !== '') {
            return trim((string) $getenv);
        }

        $file = self::readEnvFile()[$key] ?? '';

        return trim((string) $file);
    }

    /**
     * @param  mixed  $a
     * @param  mixed  $b
     */
    private static function valuesDiffer($a, $b): bool
    {
        if (is_bool($a) || is_bool($b)) {
            return filter_var($a, FILTER_VALIDATE_BOOLEAN) !== filter_var($b, FILTER_VALIDATE_BOOLEAN);
        }

        return trim((string) $a) !== trim((string) $b);
    }
}
