<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
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
            if (! File::isDirectory($path)) {
                File::ensureDirectoryExists($path, 0775, true);
            }
            $writable = is_writable($path);
            $checks[] = [
                'label' => 'Writable: '.str_replace(base_path().'/', '', $path),
                'ok' => $writable,
                'message' => $writable ? 'OK' : 'Not writable',
                'required' => true,
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

        if (! File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }

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

        if (Schema::hasTable('oauth_clients') && DB::table('oauth_clients')->count() === 0) {
            Artisan::call('passport:install', ['--force' => true]);
        }
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
     * @param  array<string, string>  $mail
     */
    public function writeMailConfig(array $mail): void
    {
        $driver = $mail['mail_mailer'] ?? 'log';
        $values = [
            'MAIL_MAILER' => $driver,
            'MAIL_FROM_ADDRESS' => $mail['mail_from_address'] ?? 'noreply@localhost',
            'MAIL_FROM_NAME' => $mail['mail_from_name'] ?? env('APP_NAME', 'Knowledge Hub'),
        ];

        if ($driver === 'smtp') {
            $values['MAIL_HOST'] = $mail['mail_host'] ?? '';
            $values['MAIL_PORT'] = (string) ($mail['mail_port'] ?? '587');
            $values['MAIL_USERNAME'] = $mail['mail_username'] ?? '';
            $values['MAIL_PASSWORD'] = $mail['mail_password'] ?? '';
            $encryption = $mail['mail_encryption'] ?? 'tls';
            $values['MAIL_ENCRYPTION'] = $encryption === 'none' ? '' : $encryption;
        } elseif ($driver === 'log') {
            $values['MAIL_HOST'] = '';
            $values['MAIL_PORT'] = '';
            $values['MAIL_USERNAME'] = '';
            $values['MAIL_PASSWORD'] = '';
            $values['MAIL_ENCRYPTION'] = '';
        }

        $this->writeEnvValues($values);
        Artisan::call('config:clear');
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
                'is_approved' => 1,
                'is_verified' => 1,
                'status' => 1,
                'photo' => 'avatar.jpg',
                'is_photo_external' => 0,
            ]
        );

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
            $values['APP_URL'] = rtrim($appUrl, '/').'/';
        }
        $this->writeEnvValues($values);

        File::ensureDirectoryExists(dirname(config('install.lock_file')));
        File::put(config('install.lock_file'), now()->toIso8601String());

        $this->lockInstallerInSettings();

        Artisan::call('config:clear');
        if (! config('app.debug')) {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
        }
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
