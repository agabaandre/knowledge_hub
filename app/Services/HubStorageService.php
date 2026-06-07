<?php

namespace App\Services;

use App\Filesystem\SharePointGraphAdapter;
use App\Filesystem\SftpAdapter;
use App\Models\HubStorageSetting;
use App\Support\HubSiteIdentifier;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HubStorageService
{
    public function settings(): HubStorageSetting
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('hub_storage_settings')) {
            return new HubStorageSetting([
                'files_driver' => 'internal',
                'local_files_root' => $this->defaultInternalRoot(),
                'sql_backup_root' => $this->defaultSqlBackupRoot(),
                'auto_sql_backup' => true,
            ]);
        }

        return HubStorageSetting::current();
    }

    public function siteStorageId(): string
    {
        $override = trim((string) env('HUB_SITE_ID', ''));
        if ($override !== '') {
            return HubSiteIdentifier::sanitize($override);
        }

        if (Schema::hasTable('hub_storage_settings')) {
            $stored = HubStorageSetting::query()->value('site_storage_id');
            if (is_string($stored) && $stored !== '') {
                return $stored;
            }
        }

        return HubSiteIdentifier::fromAppUrl();
    }

    public function persistSiteStorageId(): string
    {
        $id = $this->siteStorageId();

        if (! Schema::hasTable('hub_storage_settings')) {
            return $id;
        }

        $record = HubStorageSetting::query()->first();
        if ($record === null) {
            return $id;
        }

        if (empty($record->site_storage_id)) {
            $record->forceFill(['site_storage_id' => $id])->save();
        }

        return (string) ($record->site_storage_id ?: $id);
    }

    /**
     * @return array{files: string, sql_backups: string, site_root: string, site_id: string}
     */
    public function recommendedPaths(): array
    {
        $paths = HubSiteIdentifier::defaultPaths($this->siteStorageId());

        return [
            'site_id' => $this->siteStorageId(),
            'site_root' => $paths['site_root'],
            'files' => $paths['files'],
            'sql_backups' => $paths['sql_backups'],
        ];
    }

    public function legacyInternalRoot(): string
    {
        return storage_path('app/public');
    }

    public function defaultInternalRoot(): string
    {
        $env = trim((string) env('HUB_FILES_ROOT', ''));
        if ($env !== '') {
            return rtrim($env, '/\\');
        }

        return $this->recommendedPaths()['files'];
    }

    public function defaultSqlBackupRoot(): string
    {
        $env = trim((string) env('HUB_SQL_BACKUP_ROOT', ''));
        if ($env !== '') {
            return rtrim($env, '/\\');
        }

        return $this->recommendedPaths()['sql_backups'];
    }

    /**
     * Admin-configured internal files root (host path or legacy), without legacy fallback.
     */
    public function configuredInternalRoot(): string
    {
        $settings = $this->settings();
        if ($settings->files_driver !== 'internal') {
            return $this->legacyInternalRoot();
        }

        $root = trim((string) ($settings->local_files_root ?: ''));
        if ($root !== '') {
            return rtrim($root, '/\\');
        }

        $envRoot = trim((string) env('HUB_FILES_ROOT', ''));
        if ($envRoot !== '') {
            return rtrim($envRoot, '/\\');
        }

        return $this->resolveInternalRootForInstall();
    }

    /**
     * Active internal files root. Falls back to legacy storage/app/public when the
     * configured host path is empty but legacy still contains uploads.
     */
    public function filesRoot(): string
    {
        $settings = $this->settings();
        if ($settings->files_driver !== 'internal') {
            return $this->legacyInternalRoot();
        }

        $configured = $this->configuredInternalRoot();
        $legacy = $this->legacyInternalRoot();

        if ($this->pathsDiffer($configured, $legacy)
            && $this->pathHasHubUploads($legacy)
            && ! $this->pathHasHubUploads($configured)) {
            return $legacy;
        }

        return $configured;
    }

    public function isUsingLegacyUploadFallback(): bool
    {
        if ($this->settings()->files_driver !== 'internal') {
            return false;
        }

        $configured = $this->configuredInternalRoot();
        $legacy = $this->legacyInternalRoot();

        return $this->pathsDiffer($configured, $legacy)
            && realpath($this->filesRoot()) === realpath($legacy);
    }

    public function needsLegacyToHostMigration(): bool
    {
        if ($this->settings()->files_driver !== 'internal') {
            return false;
        }

        $configured = $this->configuredInternalRoot();
        $legacy = $this->legacyInternalRoot();

        return $this->pathsDiffer($configured, $legacy) && $this->pathHasHubUploads($legacy);
    }

    protected function pathsDiffer(string $a, string $b): bool
    {
        $realA = realpath($a);
        $realB = realpath($b);

        if ($realA && $realB) {
            return $realA !== $realB;
        }

        return rtrim(str_replace('\\', '/', $a), '/') !== rtrim(str_replace('\\', '/', $b), '/');
    }

    public function sqlBackupRoot(): string
    {
        $settings = $this->settings();
        $root = trim((string) ($settings->sql_backup_root ?: ''));

        if ($root === '') {
            $root = $this->defaultSqlBackupRoot();
        }

        return rtrim($root, '/\\');
    }

    public function usesLegacyInternalRoot(): bool
    {
        return $this->settings()->files_driver === 'internal'
            && realpath($this->filesRoot()) === realpath($this->legacyInternalRoot());
    }

    /**
     * Prefer host paths outside the application/container tree. Fall back to legacy
     * storage/app/public only when uploads already live there and the host path is unused.
     */
    public function resolveInternalRootForInstall(): string
    {
        $recommended = $this->defaultInternalRoot();
        $legacy = $this->legacyInternalRoot();

        if ($this->pathHasHubUploads($legacy) && ! $this->pathHasHubUploads($recommended)) {
            return $legacy;
        }

        return $recommended;
    }

    public function pathHasHubUploads(string $root): bool
    {
        foreach (config('hub_storage.content_prefixes', []) as $prefix) {
            $absolute = rtrim($root, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $prefix);
            if (! is_dir($absolute)) {
                continue;
            }
            foreach (scandir($absolute) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    return true;
                }
            }
        }

        return false;
    }

    public function usesExternalFiles(): bool
    {
        return $this->settings()->files_driver !== 'internal';
    }

    public function registerDiskConfig(): void
    {
        $settings = $this->settings();
        $driver = $settings->files_driver ?: 'internal';

        if ($driver === 'internal') {
            Config::set('filesystems.disks.hub', [
                'driver' => 'local',
                'root' => $this->filesRoot(),
                'url' => env('APP_URL').'/hub-media',
                'visibility' => 'public',
            ]);

            return;
        }

        $cloud = $settings->cloud_config ?? [];
        $scopedPrefix = $this->siteScopedPrefix((string) ($cloud['root_prefix'] ?? 'khub'));

        if ($driver === 's3') {
            Config::set('filesystems.disks.hub', [
                'driver' => 's3',
                'key' => $cloud['key'] ?? env('AWS_ACCESS_KEY_ID'),
                'secret' => $cloud['secret'] ?? env('AWS_SECRET_ACCESS_KEY'),
                'region' => $cloud['region'] ?? env('AWS_DEFAULT_REGION'),
                'bucket' => $cloud['bucket'] ?? env('AWS_BUCKET'),
                'url' => $cloud['url'] ?? env('AWS_URL'),
                'endpoint' => $cloud['endpoint'] ?? env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => (bool) ($cloud['use_path_style_endpoint'] ?? false),
                'root' => $scopedPrefix,
                'visibility' => 'public',
            ]);

            return;
        }

        if ($driver === 'gcs') {
            Config::set('filesystems.disks.hub', [
                'driver' => 'gcs',
                'project_id' => $cloud['project_id'] ?? env('GOOGLE_CLOUD_PROJECT_ID'),
                'key_file_path' => $cloud['key_file_path'] ?? env('GOOGLE_CLOUD_KEY_FILE'),
                'bucket' => $cloud['bucket'] ?? env('GOOGLE_CLOUD_STORAGE_BUCKET'),
                'path_prefix' => $scopedPrefix,
                'storage_api_uri' => $cloud['storage_api_uri'] ?? env('GOOGLE_CLOUD_STORAGE_API_URI'),
            ]);

            return;
        }

        if ($driver === 'azure') {
            Config::set('filesystems.disks.hub', [
                'driver' => 'azure-blob',
                'connection_string' => $cloud['connection_string'] ?? env('AZURE_STORAGE_CONNECTION_STRING'),
                'name' => $cloud['account_name'] ?? env('AZURE_STORAGE_NAME'),
                'key' => $cloud['account_key'] ?? env('AZURE_STORAGE_KEY'),
                'container' => $cloud['container'] ?? env('AZURE_STORAGE_CONTAINER'),
                'url' => $cloud['url'] ?? env('AZURE_STORAGE_URL'),
                'prefix' => $scopedPrefix,
            ]);

            return;
        }

        if ($driver === 'sftp') {
            Config::set('filesystems.disks.hub', [
                'driver' => 'sftp-phpseclib',
                'host' => $cloud['host'] ?? '',
                'username' => $cloud['username'] ?? '',
                'password' => $cloud['password'] ?? null,
                'private_key' => $cloud['private_key'] ?? null,
                'passphrase' => $cloud['passphrase'] ?? null,
                'port' => (int) ($cloud['port'] ?? 22),
                'root' => $this->siteScopedRemoteRoot((string) ($cloud['root_prefix'] ?? '/khub')),
                'timeout' => 30,
            ]);

            return;
        }

        if ($driver === 'sharepoint') {
            Config::set('filesystems.disks.hub', [
                'driver' => 'sharepoint-graph',
                'tenant_id' => $cloud['tenant_id'] ?? '',
                'client_id' => $cloud['client_id'] ?? '',
                'client_secret' => $cloud['client_secret'] ?? '',
                'site_id' => $cloud['site_id'] ?? null,
                'drive_id' => $cloud['drive_id'] ?? null,
                'site_hostname' => $cloud['site_hostname'] ?? null,
                'site_path' => $cloud['site_path'] ?? null,
                'prefix' => $scopedPrefix,
            ]);
        }
    }

    public function siteScopedPrefix(string $prefix = 'khub'): string
    {
        $prefix = trim($prefix, '/');
        $siteId = $this->siteStorageId();
        if ($prefix === $siteId || str_starts_with($prefix, $siteId.'/')) {
            return $prefix;
        }

        return $prefix === '' ? $siteId : $siteId.'/'.$prefix;
    }

    public function siteScopedRemoteRoot(string $root): string
    {
        $root = rtrim($root, '/');
        $siteId = $this->siteStorageId();
        if ($root === '' || str_ends_with($root, '/'.$siteId) || $root === '/'.$siteId) {
            return $root === '' ? '/'.$siteId : $root;
        }

        return $root.'/'.$siteId;
    }

    public function ensureHostDataDirectories(): void
    {
        $paths = $this->recommendedPaths();
        $hostRoot = rtrim((string) config('hub_storage.host_data_root', '/var/khubdata'), '/\\');
        if (PHP_OS_FAMILY === 'Windows') {
            $hostRoot = rtrim((string) config('hub_storage.host_data_root_windows', 'C:\\khubdata'), '/\\');
        }

        foreach ([$hostRoot, $paths['site_root'], $paths['files'], $paths['sql_backups']] as $directory) {
            File::ensureDirectoryExists($directory, 0775, true);
        }
    }

    public function disk(): Filesystem
    {
        $this->registerDiskConfig();

        return Storage::disk('hub');
    }

    public function absolutePath(string $relative = ''): string
    {
        $relative = ltrim(str_replace(['\\', '..'], ['/', ''], $relative), '/');
        $root = $this->filesRoot();

        return $relative === '' ? $root : $root.'/'.$relative;
    }

    public function ensureDirectories(): void
    {
        $this->persistSiteStorageId();
        $this->ensureHostDataDirectories();

        foreach (config('hub_storage.content_prefixes', []) as $prefix) {
            if ($this->usesExternalFiles()) {
                if (! $this->disk()->exists($prefix)) {
                    $this->disk()->makeDirectory($prefix);
                }
            } else {
                File::ensureDirectoryExists($this->absolutePath($prefix), 0775, true);
            }
        }

        File::ensureDirectoryExists($this->sqlBackupRoot(), 0775, true);
        $this->ensurePublicStorageSymlink();
    }

    /**
     * Whether public/storage resolves to the active hub files root (symlink, junction, or legacy path).
     */
    public function publicStorageLinkOk(?string $link = null, ?string $filesRoot = null): bool
    {
        if ($this->settings()->files_driver !== 'internal') {
            return true;
        }

        $link = $link ?? public_path('storage');
        $filesRoot = $filesRoot ?? $this->filesRoot();
        $targetReal = $this->realpathOrNull($filesRoot);
        $linkReal = $this->realpathOrNull($link);

        if ($targetReal === null) {
            return false;
        }

        if ($linkReal !== null && $linkReal === $targetReal) {
            return true;
        }

        return $this->usesLegacyInternalRoot()
            && (is_link($link) || is_dir($link))
            && $linkReal === $this->realpathOrNull($this->legacyInternalRoot());
    }

    /**
     * Link public/storage to the active internal files root so /storage/... URLs keep working.
     */
    public function ensurePublicStorageSymlink(): void
    {
        if ($this->settings()->files_driver !== 'internal') {
            return;
        }

        $target = $this->filesRoot();
        $link = public_path('storage');

        if ($this->publicStorageLinkOk($link, $target)) {
            return;
        }

        if (realpath($target) === realpath($this->legacyInternalRoot())) {
            if (! File::exists($link)) {
                Artisan::call('storage:link');
            }

            return;
        }

        if (File::exists($link) && ! is_link($link) && ! is_dir($link)) {
            return;
        }

        $this->removePublicStorageLink($link);
        File::ensureDirectoryExists($target, 0775, true);

        if (PHP_OS_FAMILY === 'Windows') {
            $this->createWindowsPublicStorageLink($target, $link);

            return;
        }

        File::link($target, $link);
    }

    protected function realpathOrNull(string $path): ?string
    {
        $resolved = realpath($path);

        return $resolved !== false ? $resolved : null;
    }

    protected function removePublicStorageLink(string $link): void
    {
        if (! File::exists($link) && ! is_link($link)) {
            return;
        }

        if (PHP_OS_FAMILY === 'Windows' && is_dir($link)) {
            $winLink = str_replace('/', '\\', $link);
            exec('cmd /c rmdir '.escapeshellarg($winLink), $output, $code);

            if ($code === 0 || ! File::exists($link)) {
                return;
            }
        }

        if (is_link($link)) {
            @unlink($link);

            return;
        }

        if (is_dir($link)) {
            @rmdir($link);

            return;
        }

        @unlink($link);
    }

    protected function createWindowsPublicStorageLink(string $target, string $link): void
    {
        $winTarget = str_replace('/', '\\', $target);
        $winLink = str_replace('/', '\\', $link);

        try {
            File::link($target, $link);
            if ($this->publicStorageLinkOk($link, $target)) {
                return;
            }
        } catch (\Throwable) {
            // Fall through to junction.
        }

        $this->removePublicStorageLink($link);
        exec(
            'cmd /c mklink /J '.escapeshellarg($winLink).' '.escapeshellarg($winTarget),
            $output,
            $code
        );

        if ($code !== 0 && ! $this->publicStorageLinkOk($link, $target)) {
            throw new \RuntimeException(
                'Could not link public/storage on Windows. Enable Developer Mode or run Command Prompt as Administrator, then run: php artisan hub:link-storage'
            );
        }
    }

    public function url(string $relative): string
    {
        $relative = ltrim($relative, '/');
        if ($this->usesExternalFiles()) {
            try {
                $url = $this->disk()->url($relative);
                if (Str::startsWith($url, ['http://', 'https://'])) {
                    return $url;
                }
            } catch (\Throwable $e) {
                // Fall through to hub-media route for local external roots.
            }
        }

        if ($this->settings()->files_driver === 'internal' && $this->usesLegacyInternalRoot()) {
            return url('/storage/'.$relative);
        }

        return url('/hub-media/'.$relative);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, path: string}
     */
    public function browse(string $area, string $subPath = ''): array
    {
        $area = array_key_exists($area, config('hub_storage.content_prefixes', []))
            ? $area
            : 'publications';
        $base = config('hub_storage.content_prefixes')[$area];
        $subPath = trim(str_replace(['\\', '..'], ['/', ''], $subPath), '/');
        $relative = $subPath === '' ? $base : $base.'/'.$subPath;

        $items = [];
        if ($this->usesExternalFiles()) {
            foreach ($this->disk()->directories($relative) as $dir) {
                $items[] = [
                    'name' => basename($dir),
                    'type' => 'dir',
                    'path' => str_replace($base.'/', '', $dir),
                ];
            }
            foreach ($this->disk()->files($relative) as $file) {
                $items[] = [
                    'name' => basename($file),
                    'type' => 'file',
                    'path' => str_replace($base.'/', '', $file),
                    'size' => $this->disk()->size($file),
                ];
            }
        } else {
            $absolute = $this->absolutePath($relative);
            if (! is_dir($absolute)) {
                return ['items' => [], 'path' => $relative];
            }
            foreach (scandir($absolute) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $full = $absolute.DIRECTORY_SEPARATOR.$entry;
                $items[] = [
                    'name' => $entry,
                    'type' => is_dir($full) ? 'dir' : 'file',
                    'path' => trim(($subPath === '' ? '' : $subPath.'/').$entry, '/'),
                    'size' => is_file($full) ? filesize($full) : null,
                ];
            }
        }

        usort($items, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'dir' ? -1 : 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return ['items' => $items, 'path' => $relative];
    }

    /**
     * @return array{status: string, message: string}
     */
    public function testConnection(): array
    {
        $settings = $this->settings();
        try {
            $this->registerDiskConfig();
            if ($settings->files_driver === 'sharepoint') {
                $cloud = $settings->cloud_config ?? [];
                $adapter = new SharePointGraphAdapter([
                    'tenant_id' => $cloud['tenant_id'] ?? '',
                    'client_id' => $cloud['client_id'] ?? '',
                    'client_secret' => $cloud['client_secret'] ?? '',
                    'site_id' => $cloud['site_id'] ?? null,
                    'drive_id' => $cloud['drive_id'] ?? null,
                    'site_hostname' => $cloud['site_hostname'] ?? null,
                    'site_path' => $cloud['site_path'] ?? null,
                    'prefix' => $cloud['root_prefix'] ?? 'khub',
                ]);
                $adapter->probe();
            }
            if ($settings->files_driver === 'sftp') {
                $cloud = $settings->cloud_config ?? [];
                $adapter = new SftpAdapter([
                    'host' => $cloud['host'] ?? '',
                    'username' => $cloud['username'] ?? '',
                    'password' => $cloud['password'] ?? null,
                    'private_key' => $cloud['private_key'] ?? null,
                    'passphrase' => $cloud['passphrase'] ?? null,
                    'port' => (int) ($cloud['port'] ?? 22),
                    'root' => $cloud['root_prefix'] ?? '/khub',
                ]);
                $adapter->probe();
            }
            $disk = $this->disk();
            $probe = 'hub_probe_'.Str::random(8).'.txt';
            $disk->put($probe, 'ok');
            $disk->delete($probe);

            return ['status' => 'ok', 'message' => 'Connection successful. Read/write probe passed.'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Copy uploads from storage/app/public to the configured host files root.
     */
    public function migrateLegacyToHostPath(?callable $progress = null): array
    {
        $settings = $this->settings();
        if ($settings->files_driver !== 'internal') {
            return ['status' => 'skipped', 'message' => 'Host migration applies only to the internal driver.'];
        }

        $sourceRoot = $this->legacyInternalRoot();
        $destRoot = $this->configuredInternalRoot();

        if (! $this->pathsDiffer($sourceRoot, $destRoot)) {
            return ['status' => 'skipped', 'message' => 'Configured host path is the same as legacy storage.'];
        }

        if (! $this->pathHasHubUploads($sourceRoot)) {
            return ['status' => 'skipped', 'message' => 'No uploads found under legacy storage/app/public.'];
        }

        File::ensureDirectoryExists($destRoot, 0775, true);
        $files = $this->collectUploadFilesUnderRoot($sourceRoot);

        $settings->update([
            'migration_status' => 'running',
            'migration_files_total' => count($files),
            'migration_files_done' => 0,
            'migration_message' => 'Copying uploads to '.$destRoot,
        ]);

        $done = 0;
        foreach ($files as $relative) {
            $source = $sourceRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $target = $destRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            File::ensureDirectoryExists(dirname($target), 0775, true);

            if (! is_file($source)) {
                continue;
            }

            if (! is_file($target)) {
                File::copy($source, $target);
            }

            $done++;
            if ($progress) {
                $progress($done, count($files), $relative);
            }
            if ($done % 25 === 0) {
                $settings->update(['migration_files_done' => $done]);
            }
        }

        $settings->update([
            'migration_status' => 'completed',
            'migration_files_done' => $done,
            'migration_message' => "Copied {$done} file(s) to host path {$destRoot}. Originals kept under legacy storage.",
        ]);

        $this->ensurePublicStorageSymlink();

        return ['status' => 'completed', 'files' => $done, 'destination' => $destRoot];
    }

    public function migrateInternalToExternal(?callable $progress = null): array
    {
        $settings = $this->settings();
        if ($settings->files_driver === 'internal') {
            return ['status' => 'skipped', 'message' => 'Files driver is still internal.'];
        }

        $sourceRoot = $this->filesRoot();
        if (! $this->pathHasHubUploads($sourceRoot)) {
            $sourceRoot = $this->legacyInternalRoot();
        }
        $files = $this->collectUploadFilesUnderRoot($sourceRoot);

        $settings->update([
            'migration_status' => 'running',
            'migration_files_total' => count($files),
            'migration_files_done' => 0,
            'migration_message' => 'Migrating to '.$settings->files_driver.' storage',
        ]);

        $done = 0;
        $disk = $this->disk();
        foreach ($files as $relative) {
            $source = $sourceRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (! is_file($source)) {
                continue;
            }
            $stream = fopen($source, 'r');
            if ($stream) {
                $disk->put($relative, $stream);
                fclose($stream);
            }
            $done++;
            if ($progress) {
                $progress($done, count($files), $relative);
            }
            if ($done % 25 === 0) {
                $settings->update(['migration_files_done' => $done]);
            }
        }

        $settings->update([
            'migration_status' => 'completed',
            'migration_files_done' => $done,
            'migration_message' => "Migrated {$done} file(s) to {$settings->files_driver} storage.",
        ]);

        return ['status' => 'completed', 'files' => $done];
    }

    /**
     * @return list<string> paths relative to $root (e.g. uploads/publications/foo.pdf)
     */
    protected function collectUploadFilesUnderRoot(string $root): array
    {
        $prefixes = array_values(config('hub_storage.content_prefixes', []));
        $files = [];

        foreach ($prefixes as $prefix) {
            $dir = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $prefix);
            if (! is_dir($dir)) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $full = $fileInfo->getPathname();
                    $relative = $prefix.'/'.substr($full, strlen($dir) + 1);
                    $files[] = str_replace('\\', '/', $relative);
                }
            }
        }

        return $files;
    }
}
