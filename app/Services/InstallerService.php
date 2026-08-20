<?php

namespace App\Services;

use App\Models\HubStorageSetting;
use App\Models\Setting;
use App\Models\User;
use App\Support\EmailConfig;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class InstallerService
{
    public function isInstalled(): bool
    {
        if (filter_var(env('APP_INSTALLED', false), FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        if (File::exists(config('install.lock_file'))) {
            return true;
        }

        return $this->isLockedInSettings();
    }

    public function isLockedInSettings(): bool
    {
        try {
            if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'installer_locked')) {
                return false;
            }

            return Setting::query()->where('installer_locked', 1)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public function hasVendorPackages(): bool
    {
        return File::isDirectory(base_path('vendor'))
            && File::exists(base_path('vendor/autoload.php'));
    }

    /**
     * True when Composer dependencies are present and the configured database has populated tables.
     */
    public function isExistingDeployment(): bool
    {
        if ($this->isInstalled()) {
            return true;
        }

        if (! $this->hasVendorPackages()) {
            return false;
        }

        return $this->databaseHasExistingData($this->databaseDefaults());
    }

    /**
     * @param  array<string, mixed>|null  $db
     */
    public function databaseHasExistingTables(?array $db = null): bool
    {
        return count($this->listDatabaseTables($db)) > 0;
    }

    /**
     * @param  array<string, mixed>|null  $db
     */
    public function databaseHasExistingData(?array $db = null): bool
    {
        try {
            foreach ($this->listDatabaseTables($db) as $table) {
                if ($this->tableRowCount($table, $db) > 0) {
                    return true;
                }
            }
        } catch (\Throwable) {
            return false;
        }

        return false;
    }

    public function runtimeEnvironment(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            if (filter_var(env('DOCKER', false), FILTER_VALIDATE_BOOLEAN)) {
                return 'docker';
            }

            return 'windows';
        }

        if (filter_var(env('DOCKER', false), FILTER_VALIDATE_BOOLEAN)) {
            return 'docker';
        }
        if (file_exists('/.dockerenv')) {
            return 'docker';
        }
        if (in_array((string) env('DB_HOST', ''), ['mysql', 'db', 'mariadb'], true)) {
            return 'docker';
        }

        return 'local';
    }

    /**
     * @return array{host: string, port: string, database: string, username: string, password: string}
     */
    public function databaseDefaults(): array
    {
        $env = $this->runtimeEnvironment();
        $configured = config("install.database_defaults.{$env}", config('install.database_defaults.local'));

        return [
            'host' => env('DB_HOST', $configured['host']),
            'port' => (string) env('DB_PORT', $configured['port']),
            'database' => env('DB_DATABASE', $configured['database']),
            'username' => env('DB_USERNAME', $configured['username']),
            'password' => env('DB_PASSWORD', $configured['password']),
        ];
    }

    /**
     * @return array{files_root: string, sql_backup_root: string}
     */
    public function storageDefaults(): array
    {
        $env = $this->runtimeEnvironment();
        $configured = config("install.storage_defaults.{$env}", config('install.storage_defaults.local'));
        $storage = app(HubStorageService::class);

        return [
            'files_root' => env('HUB_FILES_ROOT', $configured['files_root'] ?? $storage->defaultInternalRoot()),
            'sql_backup_root' => env('HUB_SQL_BACKUP_ROOT', $configured['sql_backup_root'] ?? $storage->defaultSqlBackupRoot()),
        ];
    }

    /**
     * Installer step 1 — server / filesystem checks (Docker and bare metal).
     *
     * @return array{ok: bool, runtime: string, checks: array<int, array{label: string, ok: bool, message: string, required: bool}>}
     */
    public function requirements(): array
    {
        $checks = [];
        $runtime = $this->runtimeEnvironment();

        $checks[] = [
            'label' => 'Runtime environment',
            'ok' => true,
            'message' => $runtime === 'docker' ? 'Docker / container' : 'Local server (non-Docker)',
            'required' => false,
        ];

        $phpOk = version_compare(PHP_VERSION, config('install.required_php'), '>=');
        $checks[] = [
            'label' => 'PHP '.config('install.required_php').'+',
            'ok' => $phpOk,
            'message' => 'Current: '.PHP_VERSION,
            'required' => true,
        ];

        foreach (config('install.required_extensions') as $ext) {
            $loaded = extension_loaded($ext);
            $checks[] = [
                'label' => "PHP extension: {$ext}",
                'ok' => $loaded,
                'message' => $loaded ? 'Loaded' : 'Missing',
                'required' => true,
            ];
        }

        foreach (config('install.recommended_extensions', []) as $ext => $hint) {
            $loaded = extension_loaded($ext);
            $checks[] = [
                'label' => "Recommended: {$ext}",
                'ok' => $loaded,
                'message' => $loaded ? 'Loaded' : $hint,
                'required' => false,
            ];
        }

        $envPath = base_path('.env');
        $envWritable = File::exists($envPath) ? is_writable($envPath) : is_writable(base_path());
        $checks[] = [
            'label' => '.env writable',
            'ok' => $envWritable,
            'message' => $envWritable ? 'OK' : 'Cannot write .env — fix permissions on project root',
            'required' => true,
        ];

        $vendorOk = File::isDirectory(base_path('vendor'));
        $checks[] = [
            'label' => 'Composer dependencies (vendor/)',
            'ok' => $vendorOk,
            'message' => $vendorOk ? 'Installed' : 'Run: composer install',
            'required' => true,
        ];

        $keyOk = ! empty(env('APP_KEY')) && str_starts_with((string) env('APP_KEY'), 'base64:');
        $checks[] = [
            'label' => 'Application key (APP_KEY)',
            'ok' => $keyOk,
            'message' => $keyOk ? 'Set' : 'Will be generated during database setup',
            'required' => false,
        ];

        foreach (config('install.writable_paths') as $path) {
            $createError = null;
            if (! File::isDirectory($path)) {
                try {
                    File::ensureDirectoryExists($path, 0775, true);
                } catch (\Throwable $e) {
                    $createError = $e->getMessage();
                }
            }
            $writable = is_dir($path) && is_writable($path);
            $relative = str_replace(base_path().'/', '', $path);
            $fixHint = 'sudo mkdir -p storage/app storage/logs storage/framework/{cache,sessions,views} bootstrap/cache public/uploads'
                .' && sudo chown -R www-data:www-data storage bootstrap/cache public/uploads'
                .' && sudo chmod -R ug+rwx storage bootstrap/cache public/uploads';
            $checks[] = [
                'label' => 'Writable: '.$relative,
                'ok' => $writable,
                'message' => $writable
                    ? 'OK'
                    : ('Not writable'
                        .($createError ? ' — '.$createError : '')
                        .'. Fix with: '.$fixHint),
                'required' => true,
            ];
        }

        $hubStorage = app(HubStorageService::class);
        $recommended = $hubStorage->recommendedPaths();
        $storageDefaults = $this->storageDefaults();
        foreach ([
            'site_root' => $recommended['site_root'],
            'files' => $storageDefaults['files_root'] ?: $recommended['files'],
            'sql_backups' => $storageDefaults['sql_backup_root'] ?: $recommended['sql_backups'],
        ] as $label => $path) {
            $createError = null;
            if (! File::isDirectory($path)) {
                try {
                    File::ensureDirectoryExists($path, 0775, true);
                } catch (\Throwable $e) {
                    $createError = $e->getMessage();
                }
            }
            $writable = is_dir($path) && is_writable($path);
            $checks[] = [
                'label' => "Host data path ({$label}): {$path}",
                'ok' => $writable,
                'message' => $writable
                    ? 'Writable (site id: '.$recommended['site_id'].')'
                    : ($runtime === 'docker'
                        ? 'Create and mount a host volume at /var/khubdata (see docker-compose)'
                        : ($runtime === 'windows'
                            ? 'Create the folder and grant the web server write access: '.$path
                            : 'Run: sudo mkdir -p '.$path
                                .' && sudo chown -R www-data:www-data '.dirname($recommended['site_root']).'/'.$recommended['site_id']
                                .' && sudo chmod -R ug+rwx '.dirname($recommended['site_root']).'/'.$recommended['site_id']
                                .($createError ? ' — '.$createError : ''))),
                'required' => $runtime !== 'docker',
            ];
        }

        $ok = collect($checks)->every(fn (array $c) => ! $c['required'] || $c['ok']);

        return ['ok' => $ok, 'runtime' => $runtime, 'checks' => $checks];
    }

    /**
     * Post-install health checks before serving the application.
     *
     * @return array{ok: bool, checks: array<int, array{label: string, ok: bool, message: string}>}
     */
    public function applicationPrerequisites(): array
    {
        $checks = [];

        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $checks[] = ['label' => 'Database connection', 'ok' => true, 'message' => 'Connected'];
        } catch (\Throwable $e) {
            $checks[] = ['label' => 'Database connection', 'ok' => false, 'message' => $e->getMessage()];
        }

        $checks[] = [
            'label' => 'Active site settings',
            'ok' => $this->hasActiveSiteSettings(),
            'message' => $this->hasActiveSiteSettings() ? 'Configured' : 'No active setting row — re-run installer or add settings in admin',
        ];

        $storageOk = is_writable(storage_path());
        $checks[] = [
            'label' => 'Storage writable',
            'ok' => $storageOk,
            'message' => $storageOk ? 'OK' : 'Fix permissions on storage/',
        ];

        try {
            $hubStorage = app(HubStorageService::class);
            $filesRoot = $hubStorage->filesRoot();
            $filesWritable = is_dir($filesRoot) && is_writable($filesRoot);
            $checks[] = [
                'label' => 'Hub files root',
                'ok' => $filesWritable,
                'message' => $filesWritable ? $filesRoot : 'Not writable: '.$filesRoot,
            ];
            $link = public_path('storage');
            $linkOk = $hubStorage->publicStorageLinkOk($link, $filesRoot);
            $checks[] = [
                'label' => 'public/storage symlink',
                'ok' => $linkOk || $hubStorage->usesLegacyInternalRoot(),
                'message' => $linkOk
                    ? 'Linked to '.$filesRoot
                    : ($hubStorage->usesLegacyInternalRoot()
                        ? 'Using legacy storage/app/public'
                        : (PHP_OS_FAMILY === 'Windows'
                            ? 'Run installer storage step or php artisan hub:link-storage (or link-hub-storage.bat)'
                            : 'Run installer storage step, ./link-hub-storage.sh, or php artisan hub:link-storage')),
            ];
        } catch (\Throwable $e) {
            $checks[] = [
                'label' => 'Hub files root',
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        $vendorOk = File::isDirectory(base_path('vendor'));
        $checks[] = [
            'label' => 'Composer dependencies',
            'ok' => $vendorOk,
            'message' => $vendorOk ? 'Present' : 'Run composer install',
        ];

        $ok = collect($checks)->every(fn (array $c) => $c['ok']);

        return ['ok' => $ok, 'checks' => $checks];
    }

    public function hasActiveSiteSettings(): bool
    {
        try {
            if (! Schema::hasTable('setting')) {
                return false;
            }
            $setting = Setting::query()->where('status', 'active')->first()
                ?? Setting::query()->orderBy('id')->first();

            return $setting !== null && trim((string) $setting->site_name) !== '';
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $db
     * @return array{ok: bool, message: string}
     */
    public function testDatabaseConnection(array $db): array
    {
        try {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s', $db['host'], $db['port'], $db['database']),
                $db['username'],
                $db['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $pdo->query('SELECT 1');

            return ['ok' => true, 'message' => 'Database connection successful.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, string>  $values
     */
    public function writeEnvValues(array $values): void
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            foreach ([base_path('.env.docker.example'), base_path('.env.example')] as $example) {
                if (File::exists($example)) {
                    File::copy($example, $envPath);
                    break;
                }
            }
        }

        if (! File::exists($envPath)) {
            File::put($envPath, "APP_NAME=\"Knowledge Hub\"\nAPP_KEY=\nAPP_INSTALLED=false\n");
        }

        $contents = File::get($envPath);
        foreach ($values as $key => $value) {
            $escaped = $this->escapeEnvValue((string) $value);
            $pattern = '/^'.preg_quote($key, '/').'=.*/m';
            $line = $key.'='.$escaped;
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents);
            } else {
                $contents .= PHP_EOL.$line;
            }
        }
        File::put($envPath, rtrim($contents).PHP_EOL);
    }

    /**
     * @param  list<string>  $keys
     */
    public function clearEnvKeys(array $keys): void
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = '';
        }
        $this->writeEnvValues($values);
    }

    /**
     * Remove mail overrides from .env so database settings in admin configure take effect.
     */
    public function clearMailEnvOverrides(): void
    {
        $this->clearEnvKeys(array_merge(
            config('install.mail_env_keys', []),
            config('install.exchange_env_keys', [])
        ));
        Artisan::call('config:clear');
    }

    public function clearMoodleEnvOverrides(): void
    {
        $this->clearLearningEnvOverrides();
    }

    public function clearLearningEnvOverrides(): void
    {
        $this->clearEnvKeys(config('install.learning_env_keys', []));
        Artisan::call('config:clear');
    }

    public function clearSsoEnvOverrides(): void
    {
        $this->clearEnvKeys(config('install.sso_env_keys', []));
        Artisan::call('config:clear');
    }

    public function clearAiEnvOverrides(): void
    {
        $keys = \App\Support\AiConfig::envKeysToClearOnSave();
        if ($keys !== []) {
            $this->clearEnvKeys($keys);
        }
        Artisan::call('config:clear');
    }

    /**
     * @param  array<string, mixed>  $sso
     */
    public function saveSsoSettings(array $sso): Setting
    {
        $setting = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->orderBy('id')->first();

        if (! $setting) {
            throw new \RuntimeException('No site settings row found. Complete the site step first.');
        }

        if (! Schema::hasColumn('setting', 'microsoft_client_id')) {
            throw new \RuntimeException('SSO settings require a database migration. Run php artisan migrate.');
        }

        $payload = [
            'microsoft_client_id' => trim((string) ($sso['microsoft_client_id'] ?? '')),
            'microsoft_redirect_uri' => trim((string) ($sso['microsoft_redirect_uri'] ?? '')),
            'microsoft_tenant_id' => trim((string) ($sso['microsoft_tenant_id'] ?? 'common')) ?: 'common',
            'google_client_id' => trim((string) ($sso['google_client_id'] ?? '')),
            'google_redirect_uri' => trim((string) ($sso['google_redirect_uri'] ?? '')),
            'linkedin_client_id' => trim((string) ($sso['linkedin_client_id'] ?? '')),
            'linkedin_redirect_uri' => trim((string) ($sso['linkedin_redirect_uri'] ?? '')),
            'enable_microsoft_login' => (bool) ($sso['enable_microsoft_login'] ?? true),
            'enable_google_login' => (bool) ($sso['enable_google_login'] ?? true),
            'enable_linkedin_login' => (bool) ($sso['enable_linkedin_login'] ?? true),
        ];

        if (Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            $payload['sso_use_database_credentials'] = true;
        }

        if (! empty($sso['microsoft_client_secret'])) {
            $payload['microsoft_client_secret'] = $sso['microsoft_client_secret'];
        }
        if (! empty($sso['google_client_secret'])) {
            $payload['google_client_secret'] = $sso['google_client_secret'];
        }
        if (! empty($sso['linkedin_client_secret'])) {
            $payload['linkedin_client_secret'] = $sso['linkedin_client_secret'];
        }

        $setting->forceFill($payload)->save();

        $this->clearSsoEnvOverrides();

        \App\Support\SsoConfig::clearCache();
        \App\Support\SsoConfig::applyRuntimeConfig();
        Cache::forget('settings');
        Artisan::call('config:clear');

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $moodle
     */
    public function saveMoodleSettings(array $moodle): Setting
    {
        return $this->saveLearningSettings($moodle);
    }

    /**
     * @param  array<string, mixed>  $learning
     */
    public function saveLearningSettings(array $learning): Setting
    {
        $setting = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->orderBy('id')->first();

        if (! $setting) {
            throw new \RuntimeException('No site settings row found. Complete the site step first.');
        }

        if (! Schema::hasColumn('setting', 'moodle_api_url')) {
            throw new \RuntimeException('Learning settings require a database migration. Run php artisan migrate.');
        }

        $payload = [];

        if (array_key_exists('moodle_api_url', $learning)) {
            $payload['moodle_api_url'] = $learning['moodle_api_url'] ?? '';
            $payload['moodle_base_url'] = rtrim((string) ($learning['moodle_base_url'] ?? ''), '/');
            $payload['moodle_sync_enabled'] = (bool) ($learning['moodle_sync_enabled'] ?? false);
        }

        if (! empty($learning['moodle_api_token'])) {
            $payload['moodle_api_token'] = $learning['moodle_api_token'];
        }

        if (Schema::hasColumn('setting', 'frappe_base_url')) {
            $payload['frappe_base_url'] = rtrim((string) ($learning['frappe_base_url'] ?? ''), '/');
            $payload['frappe_api_key'] = $learning['frappe_api_key'] ?? '';
            $payload['frappe_course_doctype'] = $learning['frappe_course_doctype'] ?? 'LMS Course';
            $payload['frappe_sync_enabled'] = (bool) ($learning['frappe_sync_enabled'] ?? false);
            if (! empty($learning['frappe_api_secret'])) {
                $payload['frappe_api_secret'] = $learning['frappe_api_secret'];
            }
        }

        if (Schema::hasColumn('setting', 'openedx_lms_url')) {
            $payload['openedx_lms_url'] = rtrim((string) ($learning['openedx_lms_url'] ?? ''), '/');
            $payload['openedx_client_id'] = $learning['openedx_client_id'] ?? '';
            $payload['openedx_token_url'] = $learning['openedx_token_url'] ?? '';
            $payload['openedx_sync_enabled'] = (bool) ($learning['openedx_sync_enabled'] ?? false);
            if (! empty($learning['openedx_client_secret'])) {
                $payload['openedx_client_secret'] = $learning['openedx_client_secret'];
            }
        }

        $setting->forceFill($payload)->save();

        $this->clearLearningEnvOverrides();

        \App\Support\LearningConfig::clearAllCaches();
        \App\Support\LearningConfig::applyRuntimeConfig();
        Cache::forget('settings');
        Artisan::call('config:clear');

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $db
     */
    public function configureDatabaseConnection(array $db): void
    {
        $this->writeEnvValues([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => (string) $db['host'],
            'DB_PORT' => (string) $db['port'],
            'DB_DATABASE' => (string) $db['database'],
            'DB_USERNAME' => (string) $db['username'],
            'DB_PASSWORD' => (string) $db['password'],
            'APP_INSTALLED' => 'false',
        ]);

        Artisan::call('config:clear');

        $envContents = File::exists(base_path('.env')) ? File::get(base_path('.env')) : '';
        if (! str_contains($envContents, 'APP_KEY=base64:')) {
            Artisan::call('key:generate', ['--force' => true]);
        }
    }

    /**
     * @param  array<string, mixed>  $db
     */
    public function runDatabaseSetup(array $db, bool $runMigrations = true): void
    {
        $this->configureDatabaseConnection($db);

        if (! $runMigrations) {
            return;
        }

        Artisan::call('migrate', ['--force' => true]);

        if (Schema::hasTable('roles') && Role::query()->count() === 0) {
            $this->importBaselineSeed();
        }

        if (Schema::hasTable('access_levels') && DB::table('access_levels')->count() === 0) {
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\AccessLevelsSeeder', '--force' => true]);
        }

        $this->ensurePassportKeys();

        if (Schema::hasTable('oauth_clients') && DB::table('oauth_clients')->count() === 0) {
            Artisan::call('passport:install', ['--force' => true]);
            $this->securePassportKeyPermissions();
        }
    }

    /**
     * Provisioned country hubs often copy oauth_clients but exclude key files.
     * Always ensure storage/oauth-*.key exist before Passport boots.
     */
    public function ensurePassportKeys(): void
    {
        \App\Support\PassportKeyGenerator::ensureKeysExist();
        $this->securePassportKeyPermissions();
    }

    protected function securePassportKeyPermissions(): void
    {
        foreach (['oauth-private.key', 'oauth-public.key'] as $name) {
            $path = storage_path($name);
            if (is_file($path)) {
                @chmod($path, 0600);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $storage
     */
    public function configureStorage(array $storage): void
    {
        $defaults = $this->storageDefaults();
        $filesRoot = trim((string) ($storage['local_files_root'] ?? $defaults['files_root']));
        $sqlRoot = trim((string) ($storage['sql_backup_root'] ?? $defaults['sql_backup_root']));
        $driver = (string) ($storage['files_driver'] ?? 'internal');

        $hubStorage = app(HubStorageService::class);
        $siteId = $hubStorage->siteStorageId();

        $this->writeEnvValues([
            'HUB_SITE_ID' => $siteId,
            'HUB_FILES_ROOT' => $filesRoot,
            'HUB_SQL_BACKUP_ROOT' => $sqlRoot,
        ]);

        if (! Schema::hasTable('hub_storage_settings')) {
            $hubStorage->ensureHostDataDirectories();
            $hubStorage->ensurePublicStorageSymlink();
            Artisan::call('config:clear');

            return;
        }

        $cloud = array_filter([
            'key' => $storage['cloud_key'] ?? null,
            'secret' => $storage['cloud_secret'] ?? null,
            'region' => $storage['cloud_region'] ?? null,
            'bucket' => $storage['cloud_bucket'] ?? null,
            'endpoint' => $storage['cloud_endpoint'] ?? null,
            'root_prefix' => $storage['cloud_root_prefix'] ?? 'khub',
            'connection_string' => $storage['cloud_connection_string'] ?? null,
            'account_name' => $storage['cloud_account_name'] ?? null,
            'account_key' => $storage['cloud_account_key'] ?? null,
            'container' => $storage['cloud_container'] ?? null,
            'project_id' => $storage['gcs_project_id'] ?? null,
            'key_file_path' => $storage['gcs_key_file_path'] ?? null,
            'storage_api_uri' => $storage['gcs_storage_api_uri'] ?? null,
            'tenant_id' => $storage['sharepoint_tenant_id'] ?? null,
            'client_id' => $storage['sharepoint_client_id'] ?? null,
            'client_secret' => $storage['sharepoint_client_secret'] ?? null,
            'site_hostname' => $storage['sharepoint_site_hostname'] ?? null,
            'site_path' => $storage['sharepoint_site_path'] ?? null,
            'site_id' => $storage['sharepoint_site_id'] ?? null,
            'drive_id' => $storage['sharepoint_drive_id'] ?? null,
            'host' => $storage['cloud_host'] ?? null,
            'username' => $storage['cloud_username'] ?? null,
            'password' => $storage['cloud_password'] ?? null,
            'port' => $storage['cloud_port'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        $record = HubStorageSetting::query()->first();
        $payload = [
            'files_driver' => $driver,
            'site_storage_id' => $siteId,
            'local_files_root' => $driver === 'internal' ? $filesRoot : null,
            'sql_backup_root' => $sqlRoot,
            'cloud_config' => $cloud ?: null,
            'auto_sql_backup' => filter_var($storage['auto_sql_backup'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'sql_backup_retention_days' => (int) ($storage['sql_backup_retention_days'] ?? 30),
        ];

        if ($record) {
            if (empty($record->site_storage_id)) {
                $payload['site_storage_id'] = $siteId;
            }
            $record->forceFill($payload)->save();
        } else {
            HubStorageSetting::query()->create($payload);
        }

        $hubStorage->ensureDirectories();
        Artisan::call('config:clear');
    }

    /**
     * @param  array<string, string>  $site
     */
    public function saveSiteSettings(array $site): Setting
    {
        $payload = array_merge(config('install.default_site', []), [
            'status' => 'active',
            'config_name' => $site['config_name'] ?? 'Default',
            'site_name' => $site['site_name'],
            'title' => $site['title'] ?? $site['site_name'],
            'slogan' => $site['slogan'] ?? '',
            'site_description' => $site['site_description'],
            'seo_keywords' => $site['seo_keywords'] ?? $site['site_name'],
            'email' => $site['contact_email'],
            'phone' => $site['phone'] ?? '',
            'address' => $site['address'] ?? '',
            'timezone' => $site['timezone'],
        ]);

        $existing = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->orderBy('id')->first();

        Setting::query()->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))
            ->update(['status' => 'inactive']);

        if ($existing) {
            $existing->forceFill($payload)->save();
            $setting = $existing;
        } else {
            $setting = Setting::query()->create($payload);
        }

        $this->writeEnvValues([
            'APP_NAME' => $site['site_name'],
        ]);

        Cache::forget('settings');

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $mail
     */
    public function saveMailSettings(array $mail): Setting
    {
        $driver = (string) ($mail['mail_mailer'] ?? 'exchange');
        $setting = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->orderBy('id')->first();

        if (! $setting) {
            throw new \RuntimeException('No site settings row found. Complete the site step first.');
        }

        $payload = [
            'mail_from_address' => $mail['mail_from_address'] ?? 'noreply@localhost',
            'mail_from_name' => $mail['mail_from_name'] ?? env('APP_NAME', 'Knowledge Hub'),
        ];

        if (Schema::hasColumn('setting', 'email_driver')) {
            $payload['email_driver'] = $driver === 'log' ? 'exchange' : $driver;
        }

        if ($driver === 'smtp') {
            $payload['mail_host'] = $mail['mail_host'] ?? '';
            $payload['mail_port'] = (string) ($mail['mail_port'] ?? '587');
            $payload['mail_username'] = $mail['mail_username'] ?? '';
            $payload['mail_password'] = $mail['mail_password'] ?? '';
            $encryption = $mail['mail_encryption'] ?? 'tls';
            $payload['mail_encryption'] = in_array($encryption, ['tls', 'ssl', 'none'], true) ? $encryption : 'tls';
        } elseif ($driver === 'exchange') {
            $payload['exchange_tenant_id'] = $mail['exchange_tenant_id'] ?? '';
            $payload['exchange_client_id'] = $mail['exchange_client_id'] ?? '';
            $payload['exchange_client_secret'] = $mail['exchange_client_secret'] ?? '';
            $payload['exchange_redirect_uri'] = $mail['exchange_redirect_uri'] ?? '';
            $payload['exchange_scope'] = $mail['exchange_scope'] ?? 'https://graph.microsoft.com/.default';
            $authMethod = $mail['exchange_auth_method'] ?? 'client_credentials';
            $payload['exchange_auth_method'] = in_array($authMethod, ['client_credentials', 'authorization_code'], true)
                ? $authMethod
                : 'client_credentials';
        }

        $setting->forceFill($payload)->save();

        $this->clearMailEnvOverrides();

        if ($driver === 'log') {
            $this->writeEnvValues(['MAIL_MAILER' => 'log']);
        }

        EmailConfig::clearCache();
        Cache::forget('settings');
        Artisan::call('config:clear');

        return $setting;
    }

    /**
     * @param  array<string, string>  $mail
     *
     * @deprecated Use saveMailSettings() so values are stored in the database.
     */
    public function writeMailConfig(array $mail): void
    {
        $this->saveMailSettings($mail);
    }

    /**
     * @param  array<string, string>  $admin
     */
    public function createAdminUser(array $admin): User
    {
        $first = trim((string) ($admin['first_name'] ?? ''));
        $last = trim((string) ($admin['last_name'] ?? ''));
        $name = trim($first.' '.$last);
        if ($name === '') {
            $name = (string) ($admin['name'] ?? 'Administrator');
        }

        $user = User::query()->updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $name,
                'first_name' => $first !== '' ? $first : $name,
                'last_name' => $last,
                'password' => Hash::make($admin['password']),
            ]
        );

        $user->is_approved = 1;
        $user->is_verified = 1;
        $user->verification_token = null;
        $user->email_verified_at = now();
        $user->status = 1;
        $user->photo = 'avatar.jpg';
        $user->is_photo_external = 0;
        $user->save();

        $role = Role::query()->where('name', 'Admin')->first();
        if ($role && ! $user->hasRole('Admin')) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function finalize(string $appUrl = ''): void
    {
        $values = [
            'APP_INSTALLED' => 'true',
            'INSTALLER_DISABLED' => 'true',
        ];
        if ($appUrl !== '') {
            // Keep APP_URL without trailing slash; trailing slash + route:cache breaks subdirectory hubs.
            $values['APP_URL'] = rtrim($appUrl, '/');
        }
        $this->writeEnvValues($values);

        File::ensureDirectoryExists(dirname(config('install.lock_file')));
        File::put(config('install.lock_file'), now()->toIso8601String());

        $this->lockInstallerInSettings();

        try {
            $hubStorage = app(HubStorageService::class);
            if (Schema::hasTable('hub_storage_settings')) {
                $hubStorage->ensureDirectories();
            } else {
                $hubStorage->ensureHostDataDirectories();
                $hubStorage->ensurePublicStorageSymlink();
            }
        } catch (\Throwable) {
            // Storage paths may be configured on a later admin visit.
        }

        Artisan::call('config:clear');
        if (! config('app.debug')) {
            Artisan::call('config:cache');
            // Subdirectory installs (e.g. /ghana) often break with route:cache (405s).
            if (! $this->appUrlHasPath(env('APP_URL', $appUrl))) {
                try {
                    Artisan::call('route:cache');
                } catch (\Throwable) {
                    Artisan::call('route:clear');
                }
            } else {
                Artisan::call('route:clear');
            }
        }
    }

    protected function appUrlHasPath(string $appUrl): bool
    {
        $path = parse_url(rtrim($appUrl, '/'), PHP_URL_PATH);

        return is_string($path) && trim($path, '/') !== '';
    }

    public function lockInstallerInSettings(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'installer_locked')) {
            return;
        }

        $completedAt = now();
        $setting = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->orderBy('id')->first();

        if ($setting) {
            $setting->forceFill([
                'installer_locked' => 1,
                'installer_completed_at' => $completedAt,
            ])->save();

            return;
        }

        Setting::query()->create([
            'status' => 'active',
            'site_name' => env('APP_NAME', 'Knowledge Hub'),
            'seo_keywords' => '',
            'site_description' => '',
            'timezone' => 'UTC',
            'default_primary_color' => '#563D7C',
            'default_secondary_color' => '#2F2424',
            'icon_font_color' => '#2F2424',
            'installer_locked' => 1,
            'installer_completed_at' => $completedAt,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $db
     * @return array<int, string>
     */
    private function listDatabaseTables(?array $db = null): array
    {
        $pdo = $this->databasePdo($db);
        if ($pdo === null) {
            return [];
        }

        $database = $db !== null
            ? (string) $db['database']
            : (string) config('database.connections.mysql.database', '');

        if ($database === '') {
            return [];
        }

        $quoted = str_replace('`', '``', $database);
        $statement = $pdo->query('SHOW TABLES FROM `'.$quoted.'`');

        if ($statement === false) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($row) => is_array($row) ? (string) reset($row) : null,
            $statement->fetchAll(\PDO::FETCH_ASSOC)
        )));
    }

    /**
     * @param  array<string, mixed>|null  $db
     */
    private function tableRowCount(string $table, ?array $db = null): int
    {
        $pdo = $this->databasePdo($db);
        if ($pdo === null) {
            return 0;
        }

        $quotedTable = str_replace('`', '``', $table);
        $statement = $pdo->query('SELECT COUNT(*) FROM `'.$quotedTable.'`');

        return $statement === false ? 0 : (int) $statement->fetchColumn();
    }

    /**
     * @param  array<string, mixed>|null  $db
     */
    private function databasePdo(?array $db = null): ?\PDO
    {
        try {
            if ($db !== null) {
                return new \PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s',
                        $db['host'],
                        $db['port'],
                        $db['database']
                    ),
                    (string) $db['username'],
                    (string) ($db['password'] ?? ''),
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
            }

            if ((string) config('database.default') !== 'mysql') {
                return null;
            }

            $connection = config('database.connections.mysql');
            if (empty($connection['database'])) {
                return null;
            }

            return DB::connection('mysql')->getPdo();
        } catch (\Throwable) {
            return null;
        }
    }

    private function importBaselineSeed(): void
    {
        $path = database_path('install/baseline_seed.sql');
        if (! File::exists($path)) {
            return;
        }

        DB::unprepared(File::get($path));
    }

    private function escapeEnvValue(string $value): string
    {
        if ($value === '') {
            return '""';
        }
        if (preg_match('/[\s#="\'\\\\]/', $value)) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
        }

        return $value;
    }
}
