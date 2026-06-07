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
    private ?HubStorageSetting $settingsCache = null;

    private ?bool $hasSettingsTable = null;

    private ?string $filesRootCache = null;

    private ?bool $publicStorageLinkOkCache = null;

    /** @var 'storage'|'hub-media'|'mixed'|null */
    private ?string $internalUrlModeCache = null;

    /** @var array<string, bool> */
    private array $pathHasUploadsCache = [];

    protected function hubStorageSettingsTableExists(): bool
    {
        if ($this->hasSettingsTable === true) {
            return true;
        }

        if (Schema::hasTable('hub_storage_settings')) {
            return $this->hasSettingsTable = true;
        }

        return false;
    }

    public function forgetSettingsCache(): void
    {
        $this->settingsCache = null;
        $this->hasSettingsTable = null;
        HubStorageSetting::forgetCache();
    }

    public function settings(): HubStorageSetting
    {
        if ($this->settingsCache !== null) {
            return $this->settingsCache;
        }

        if (! $this->hubStorageSettingsTableExists()) {
            return $this->settingsCache = new HubStorageSetting([
                'files_driver' => 'internal',
                'local_files_root' => $this->defaultInternalRoot(),
                'sql_backup_root' => $this->defaultSqlBackupRoot(),
                'auto_sql_backup' => true,
                'sql_backup_retention_days' => 30,
            ]);
        }

        return $this->settingsCache = HubStorageSetting::current();
    }

    public function siteStorageId(): string
    {
        $override = trim((string) env('HUB_SITE_ID', ''));
        if ($override !== '') {
            return HubSiteIdentifier::sanitize($override);
        }

        if ($this->hubStorageSettingsTableExists()) {
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

        if (! $this->hubStorageSettingsTableExists()) {
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

    /**
     * Pre-Laravel-public-disk uploads tree (storage/uploads/…), still used on some installs.
     */
    public function deprecatedUploadsRoot(): string
    {
        return storage_path('uploads');
    }

    /**
     * Map uploads/publications/foo.pdf → storage/uploads/publications/foo.pdf
     */
    public function deprecatedAbsolutePath(string $relative): ?string
    {
        $relative = ltrim(str_replace(['\\', '..'], ['/', ''], $relative), '/');
        if (! str_starts_with($relative, 'uploads/')) {
            return null;
        }

        return $this->deprecatedUploadsRoot().'/'.substr($relative, strlen('uploads/'));
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
        if ($this->filesRootCache !== null) {
            return $this->filesRootCache;
        }

        $settings = $this->settings();
        if ($settings->files_driver !== 'internal') {
            return $this->filesRootCache = $this->legacyInternalRoot();
        }

        $configured = $this->configuredInternalRoot();
        $legacy = $this->legacyInternalRoot();

        if ($this->pathsDiffer($configured, $legacy)
            && $this->pathHasHubUploads($legacy)
            && ! $this->pathHasHubUploads($configured)) {
            return $this->filesRootCache = $legacy;
        }

        return $this->filesRootCache = $configured;
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

        if (! $this->pathsDiffer($configured, $legacy) || ! $this->pathHasHubUploads($legacy)) {
            return false;
        }

        if (! $this->pathHasHubUploads($configured)) {
            return true;
        }

        return $this->settings()->migration_status !== 'completed';
    }

    public function canPurgeLegacyInternalStorage(): bool
    {
        $preview = $this->previewPurgeLegacyInternalStorage();

        return ($preview['can_purge'] ?? false)
            && ($preview['verified'] ?? 0) > 0
            && ($preview['skipped'] ?? 0) === 0;
    }

    /**
     * @return array{
     *     can_purge: bool,
     *     legacy_root: string,
     *     host_root: string,
     *     total: int,
     *     verified: int,
     *     skipped: int,
     *     bytes: int,
     *     skipped_samples: list<string>
     * }
     */
    public function previewPurgeLegacyInternalStorage(): array
    {
        $legacyRoot = $this->legacyInternalRoot();
        $hostRoot = $this->configuredInternalRoot();

        $empty = [
            'can_purge' => false,
            'legacy_root' => $legacyRoot,
            'host_root' => $hostRoot,
            'total' => 0,
            'verified' => 0,
            'skipped' => 0,
            'bytes' => 0,
            'skipped_samples' => [],
        ];

        if ($this->settings()->files_driver !== 'internal') {
            return $empty;
        }

        if (! $this->pathsDiffer($legacyRoot, $hostRoot) || ! $this->pathHasHubUploads($legacyRoot)) {
            return $empty;
        }

        if (! $this->pathHasHubUploads($hostRoot) || $this->isUsingLegacyUploadFallback()) {
            return $empty;
        }

        $files = $this->collectUploadFilesUnderRoot($legacyRoot);
        $verified = 0;
        $skipped = 0;
        $bytes = 0;
        $skippedSamples = [];

        foreach ($files as $relative) {
            $legacyFile = $legacyRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (! is_file($legacyFile)) {
                continue;
            }

            if ($this->legacyUploadVerifiedOnHost($relative, $legacyFile, $hostRoot)) {
                $verified++;
                $bytes += (int) filesize($legacyFile);

                continue;
            }

            $skipped++;
            if (count($skippedSamples) < 5) {
                $skippedSamples[] = $relative;
            }
        }

        return [
            'can_purge' => $verified > 0,
            'legacy_root' => $legacyRoot,
            'host_root' => $hostRoot,
            'total' => count($files),
            'verified' => $verified,
            'skipped' => $skipped,
            'bytes' => $bytes,
            'skipped_samples' => $skippedSamples,
        ];
    }

    /**
     * Remove legacy upload copies after verifying each file exists on the host path.
     *
     * @return array{status: string, deleted: int, skipped: int, bytes: int, message?: string}
     */
    public function purgeLegacyInternalStorage(bool $dryRun = false): array
    {
        $preview = $this->previewPurgeLegacyInternalStorage();

        if (! ($preview['can_purge'] ?? false)) {
            return [
                'status' => 'skipped',
                'deleted' => 0,
                'skipped' => (int) ($preview['skipped'] ?? 0),
                'bytes' => 0,
                'message' => 'Legacy uploads cannot be purged yet. Complete host migration and verify files on the host path first.',
            ];
        }

        if (($preview['skipped'] ?? 0) > 0) {
            return [
                'status' => 'blocked',
                'deleted' => 0,
                'skipped' => (int) $preview['skipped'],
                'bytes' => 0,
                'message' => "{$preview['skipped']} legacy file(s) are missing or differ on the host path. Resolve mismatches before purging.",
            ];
        }

        $legacyRoot = $preview['legacy_root'];
        $hostRoot = $preview['host_root'];
        $deleted = 0;
        $bytes = 0;

        foreach ($this->collectUploadFilesUnderRoot($legacyRoot) as $relative) {
            $legacyFile = $legacyRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (! is_file($legacyFile) || ! $this->legacyUploadVerifiedOnHost($relative, $legacyFile, $hostRoot)) {
                continue;
            }

            $size = (int) filesize($legacyFile);
            if (! $dryRun) {
                File::delete($legacyFile);
            }
            $deleted++;
            $bytes += $size;
        }

        if (! $dryRun) {
            $this->removeEmptyLegacyUploadDirectories($legacyRoot);
            unset($this->pathHasUploadsCache[$legacyRoot]);
            $this->filesRootCache = null;

            $this->settings()->update([
                'migration_message' => "Removed {$deleted} legacy copy/copies from {$legacyRoot}. Active files root: {$hostRoot}.",
            ]);

            $this->ensurePublicStorageSymlink();
        }

        return [
            'status' => $dryRun ? 'dry_run' : 'completed',
            'deleted' => $deleted,
            'skipped' => 0,
            'bytes' => $bytes,
            'message' => $dryRun
                ? "Would remove {$deleted} verified legacy file(s)."
                : "Removed {$deleted} verified legacy file(s) from {$legacyRoot}.",
        ];
    }

    protected function legacyUploadVerifiedOnHost(string $relative, string $legacyFile, string $hostRoot): bool
    {
        $hostFile = $hostRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (! is_file($hostFile)) {
            return false;
        }

        return filesize($legacyFile) === filesize($hostFile);
    }

    protected function removeEmptyLegacyUploadDirectories(string $legacyRoot): void
    {
        foreach (array_values(config('hub_storage.content_prefixes', [])) as $prefix) {
            $dir = $legacyRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $prefix);
            if (! is_dir($dir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $fileInfo) {
                $path = $fileInfo->getPathname();
                if ($fileInfo->isDir() && $this->directoryIsEmpty($path)) {
                    @rmdir($path);
                }
            }

            if ($this->directoryIsEmpty($dir)) {
                @rmdir($dir);
            }
        }
    }

    protected function directoryIsEmpty(string $path): bool
    {
        if (! is_dir($path)) {
            return false;
        }

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                return false;
            }
        }

        return true;
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
        if ($this->settings()->files_driver !== 'internal') {
            return false;
        }

        if ($this->isUsingLegacyUploadFallback()) {
            return true;
        }

        $root = realpath($this->filesRoot());
        $legacy = realpath($this->legacyInternalRoot());

        return $root !== false && $legacy !== false && $root === $legacy;
    }

    /**
     * Resolve a relative uploads path to a readable file on disk (active root, then legacy).
     *
     * @return array{path: string, root: string}|null
     */
    public function resolveReadableFile(string $relative): ?array
    {
        if ($this->usesExternalFiles()) {
            $absolute = $this->absolutePath($relative);
            if (! is_file($absolute)) {
                return null;
            }
            $root = realpath($this->filesRoot());

            return $root ? ['path' => realpath($absolute) ?: $absolute, 'root' => $root] : null;
        }

        $relative = ltrim(str_replace(['\\', '..'], ['/', ''], $relative), '/');

        foreach ($this->readableInternalRoots() as $root) {
            $absolute = rtrim($root, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $resolved = $this->readableFileUnderRoot($absolute, $root);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        $deprecated = $this->deprecatedAbsolutePath($relative);
        if ($deprecated !== null) {
            $resolved = $this->readableFileUnderRoot($deprecated, $this->deprecatedUploadsRoot());
            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function readableInternalRoots(): array
    {
        $roots = [$this->filesRoot()];
        $legacy = $this->legacyInternalRoot();
        $configured = $this->configuredInternalRoot();

        foreach ([$legacy, $configured] as $candidate) {
            if ($this->pathsDiffer($candidate, $roots[0])) {
                $roots[] = $candidate;
            }
        }

        return array_values(array_unique($roots));
    }

    /**
     * @return array{path: string, root: string}|null
     */
    protected function readableFileUnderRoot(string $absolute, string $root): ?array
    {
        if (! is_file($absolute)) {
            return null;
        }
        $rootReal = realpath($root);
        $fileReal = realpath($absolute);
        if ($rootReal === false || $fileReal === false) {
            return null;
        }
        if (! str_starts_with($fileReal, $rootReal)) {
            return null;
        }

        return ['path' => $fileReal, 'root' => $rootReal];
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
        if (array_key_exists($root, $this->pathHasUploadsCache)) {
            return $this->pathHasUploadsCache[$root];
        }

        foreach (config('hub_storage.content_prefixes', []) as $prefix) {
            $absolute = rtrim($root, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $prefix);
            if (! is_dir($absolute)) {
                continue;
            }
            foreach (scandir($absolute) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    return $this->pathHasUploadsCache[$root] = true;
                }
            }
        }

        return $this->pathHasUploadsCache[$root] = false;
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
        $useDefaults = $link === null && $filesRoot === null;
        if ($useDefaults && $this->publicStorageLinkOkCache !== null) {
            return $this->publicStorageLinkOkCache;
        }

        if ($this->settings()->files_driver !== 'internal') {
            $result = true;
            if ($useDefaults) {
                $this->publicStorageLinkOkCache = $result;
            }

            return $result;
        }

        $link = $link ?? public_path('storage');
        $filesRoot = $filesRoot ?? $this->filesRoot();
        $targetReal = $this->realpathOrNull($filesRoot);
        $linkReal = $this->realpathOrNull($link);

        if ($targetReal === null) {
            $result = false;
            if ($useDefaults) {
                $this->publicStorageLinkOkCache = $result;
            }

            return $result;
        }

        if ($linkReal !== null && $linkReal === $targetReal) {
            if ($useDefaults) {
                $this->publicStorageLinkOkCache = true;
            }

            return true;
        }

        $result = $this->usesLegacyInternalRoot()
            && (is_link($link) || is_dir($link))
            && $linkReal === $this->realpathOrNull($this->legacyInternalRoot());

        if ($useDefaults) {
            $this->publicStorageLinkOkCache = $result;
        }

        return $result;
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
            $this->removePublicStorageLink($link);
            Artisan::call('storage:link');

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

        $mode = $this->internalUrlMode();
        if ($mode === 'storage') {
            return url('/storage/'.$relative);
        }
        if ($mode === 'hub-media') {
            return url('/hub-media/'.$relative);
        }

        if ($this->canServeViaPublicStorage($relative)) {
            return url('/storage/'.$relative);
        }

        return url('/hub-media/'.$relative);
    }

    /**
     * @return 'storage'|'hub-media'|'mixed'
     */
    protected function internalUrlMode(): string
    {
        if ($this->internalUrlModeCache !== null) {
            return $this->internalUrlModeCache;
        }

        if ($this->settings()->files_driver !== 'internal') {
            return $this->internalUrlModeCache = 'hub-media';
        }

        if (! $this->publicStorageLinkOk()) {
            return $this->internalUrlModeCache = 'hub-media';
        }

        $deprecated = $this->deprecatedUploadsRoot();
        if ($this->pathHasHubUploads($deprecated)
            && $this->pathsDiffer($this->filesRoot(), $deprecated)) {
            return $this->internalUrlModeCache = 'mixed';
        }

        return $this->internalUrlModeCache = 'storage';
    }

    protected function canServeViaPublicStorage(string $relative): bool
    {
        if (! $this->publicStorageLinkOk()) {
            return false;
        }

        $resolved = $this->resolveReadableFile($relative);
        if ($resolved === null) {
            return false;
        }

        $linkRoot = $this->realpathOrNull($this->filesRoot());
        if ($linkRoot === null) {
            return false;
        }

        return str_starts_with($resolved['path'], $linkRoot);
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
            $items = $this->browseInternalDirectory($relative, $subPath);
            if ($items === []) {
                return ['items' => [], 'path' => $relative];
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
     * @return array<int, array<string, mixed>>
     */
    protected function browseInternalDirectory(string $relative, string $subPath): array
    {
        $items = [];
        $seen = [];
        $directories = [$this->absolutePath($relative)];
        $deprecated = $this->deprecatedAbsolutePath($relative);
        if ($deprecated !== null) {
            $directories[] = $deprecated;
        }

        foreach ($directories as $absolute) {
            if (! is_dir($absolute)) {
                continue;
            }
            foreach (scandir($absolute) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if (isset($seen[$entry])) {
                    continue;
                }
                $seen[$entry] = true;
                $full = $absolute.DIRECTORY_SEPARATOR.$entry;
                $items[] = [
                    'name' => $entry,
                    'type' => is_dir($full) ? 'dir' : 'file',
                    'path' => trim(($subPath === '' ? '' : $subPath.'/').$entry, '/'),
                    'size' => is_file($full) ? filesize($full) : null,
                ];
            }
        }

        return $items;
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
