<?php

namespace App\Services;

use App\Filesystem\FtpAdapter;
use App\Filesystem\SftpAdapter;
use App\Models\HubStorageSetting;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class HubOffsiteBackupService
{
    public function __construct(
        private HubStorageService $storage,
        private HubDatabaseBackupService $databaseBackup
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return $this->storage->settings()->offsite_backup_config ?? [];
    }

    public function isEnabled(): bool
    {
        $config = $this->config();

        return ! empty($config['enabled']) && ! empty($config['driver']);
    }

    /**
     * @return array{status: string, message: string}
     */
    public function testConnection(): array
    {
        if (empty($this->config()['driver'])) {
            return ['status' => 'error', 'message' => 'Select an offsite backup driver first.'];
        }

        try {
            $this->registerDiskConfig();
            $driver = (string) $this->config()['driver'];
            if ($driver === 'sftp') {
                $this->probeSftp();
            } elseif ($driver === 'ftp') {
                $this->probeFtp();
            }
            $disk = $this->disk();
            $probe = 'hub_offsite_probe_'.Str::random(8).'.txt';
            $disk->put($probe, 'ok');
            $disk->delete($probe);

            return ['status' => 'ok', 'message' => 'Offsite backup connection successful. Read/write probe passed.'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Run a full local SQL backup, archive it, and upload to the configured remote.
     *
     * @return array{status: string, message: string, remote_path?: string, local_backup?: string}
     */
    public function runWeeklyUpload(bool $runLocalFullBackup = true): array
    {
        if (! $this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'Offsite backup is disabled.'];
        }

        try {
            $backup = $runLocalFullBackup
                ? $this->databaseBackup->runBackup(false)
                : $this->latestFullBackupPayload();

            if ($backup === null) {
                throw new \RuntimeException('No full SQL backup is available to upload.');
            }

            $archivePath = $this->createArchive(
                $backup['path'],
                $backup['env_backup']['path'] ?? null
            );

            $remotePath = $this->uploadArchive($archivePath, $backup['path']);
            @unlink($archivePath);

            $this->recordUploadResult('ok', 'Uploaded to '.$remotePath, $remotePath);

            return [
                'status' => 'ok',
                'message' => 'Offsite backup uploaded to '.$remotePath,
                'remote_path' => $remotePath,
                'local_backup' => $backup['path'],
            ];
        } catch (\Throwable $e) {
            $this->recordUploadResult('error', $e->getMessage());

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function registerDiskConfig(): void
    {
        $config = $this->config();
        $driver = (string) ($config['driver'] ?? '');
        if ($driver === '') {
            throw new \RuntimeException('Offsite backup driver is not configured.');
        }

        $scopedPrefix = $this->storage->siteScopedPrefix((string) ($config['remote_prefix'] ?? 'sql-backups'));

        if ($driver === 's3') {
            Config::set('filesystems.disks.hub-offsite-backup', [
                'driver' => 's3',
                'key' => $config['key'] ?? env('AWS_ACCESS_KEY_ID'),
                'secret' => $config['secret'] ?? env('AWS_SECRET_ACCESS_KEY'),
                'region' => $config['region'] ?? env('AWS_DEFAULT_REGION'),
                'bucket' => $config['bucket'] ?? env('AWS_BUCKET'),
                'url' => $config['url'] ?? env('AWS_URL'),
                'endpoint' => $config['endpoint'] ?? env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => (bool) ($config['use_path_style_endpoint'] ?? false),
                'root' => $scopedPrefix,
            ]);

            return;
        }

        if ($driver === 'gcs') {
            Config::set('filesystems.disks.hub-offsite-backup', [
                'driver' => 'gcs',
                'project_id' => $config['project_id'] ?? env('GOOGLE_CLOUD_PROJECT_ID'),
                'key_file_path' => $config['key_file_path'] ?? env('GOOGLE_CLOUD_KEY_FILE'),
                'bucket' => $config['bucket'] ?? env('GOOGLE_CLOUD_STORAGE_BUCKET'),
                'path_prefix' => $scopedPrefix,
                'storage_api_uri' => $config['storage_api_uri'] ?? env('GOOGLE_CLOUD_STORAGE_API_URI'),
            ]);

            return;
        }

        if ($driver === 'azure') {
            Config::set('filesystems.disks.hub-offsite-backup', [
                'driver' => 'azure-blob',
                'connection_string' => $config['connection_string'] ?? env('AZURE_STORAGE_CONNECTION_STRING'),
                'name' => $config['account_name'] ?? env('AZURE_STORAGE_NAME'),
                'key' => $config['account_key'] ?? env('AZURE_STORAGE_KEY'),
                'container' => $config['container'] ?? env('AZURE_STORAGE_CONTAINER'),
                'url' => $config['url'] ?? env('AZURE_STORAGE_URL'),
                'prefix' => $scopedPrefix,
            ]);

            return;
        }

        if ($driver === 'sftp') {
            Config::set('filesystems.disks.hub-offsite-backup', [
                'driver' => 'sftp-phpseclib',
                'host' => $config['host'] ?? '',
                'username' => $config['username'] ?? '',
                'password' => $config['password'] ?? null,
                'private_key' => $config['private_key'] ?? null,
                'passphrase' => $config['passphrase'] ?? null,
                'port' => (int) ($config['port'] ?? 22),
                'root' => $this->storage->siteScopedRemoteRoot((string) ($config['remote_root'] ?? '/khub-backups')),
                'timeout' => 60,
            ]);

            return;
        }

        if ($driver === 'ftp') {
            Config::set('filesystems.disks.hub-offsite-backup', [
                'driver' => 'ftp-php',
                'host' => $config['host'] ?? '',
                'username' => $config['username'] ?? '',
                'password' => $config['password'] ?? null,
                'port' => (int) ($config['port'] ?? 21),
                'ssl' => (bool) ($config['ssl'] ?? false),
                'passive' => (bool) ($config['passive'] ?? true),
                'root' => $this->storage->siteScopedRemoteRoot((string) ($config['remote_root'] ?? '/khub-backups')),
                'path_prefix' => $scopedPrefix,
                'timeout' => 60,
            ]);

            return;
        }

        throw new \RuntimeException('Unsupported offsite backup driver: '.$driver);
    }

    public function disk(): Filesystem
    {
        $this->registerDiskConfig();

        return Storage::disk('hub-offsite-backup');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function latestFullBackupPayload(): ?array
    {
        foreach ($this->databaseBackup->listBackups() as $backup) {
            if (($backup['type'] ?? '') !== 'full') {
                continue;
            }

            return [
                'path' => $backup['path'],
                'tables' => count($this->databaseBackup->tablesInBackupDirectory($backup['path'])),
                'incremental' => false,
                'env_backup' => null,
            ];
        }

        return null;
    }

    private function createArchive(string $backupDirectory, ?string $envFilePath = null): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new \RuntimeException('PHP Zip extension is required for offsite SQL backups.');
        }

        $tempDir = $this->storage->sqlBackupRoot().DIRECTORY_SEPARATOR.'offsite-temp';
        File::ensureDirectoryExists($tempDir, 0775, true);

        $archivePath = $tempDir.DIRECTORY_SEPARATOR.basename($backupDirectory).'.zip';
        if (is_file($archivePath)) {
            @unlink($archivePath);
        }

        $zip = new ZipArchive();
        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create backup archive.');
        }

        $baseName = basename($backupDirectory);
        foreach (File::allFiles($backupDirectory) as $file) {
            $relative = $baseName.'/'.$file->getRelativePathname();
            $zip->addFile($file->getPathname(), str_replace('\\', '/', $relative));
        }

        if ($envFilePath && is_file($envFilePath)) {
            $zip->addFile($envFilePath, $baseName.'/env/'.basename($envFilePath));
        } else {
            $envDir = $this->storage->sqlBackupRoot().DIRECTORY_SEPARATOR.'env';
            if (is_dir($envDir)) {
                foreach (File::files($envDir) as $envFile) {
                    if (str_starts_with($envFile->getFilename(), '.env.')) {
                        $zip->addFile($envFile->getPathname(), $baseName.'/env/'.$envFile->getFilename());
                    }
                }
            }
        }

        $zip->close();

        return $archivePath;
    }

    private function uploadArchive(string $archivePath, string $backupDirectory): string
    {
        $remoteName = 'full/'.basename($backupDirectory).'.zip';
        $stream = fopen($archivePath, 'r');
        if ($stream === false) {
            throw new \RuntimeException('Could not read backup archive for upload.');
        }

        try {
            $this->disk()->put($remoteName, $stream);
        } finally {
            fclose($stream);
        }

        return $remoteName;
    }

    private function probeSftp(): void
    {
        $config = $this->config();
        $adapter = new SftpAdapter([
            'host' => $config['host'] ?? '',
            'username' => $config['username'] ?? '',
            'password' => $config['password'] ?? null,
            'private_key' => $config['private_key'] ?? null,
            'passphrase' => $config['passphrase'] ?? null,
            'port' => (int) ($config['port'] ?? 22),
            'root' => $this->storage->siteScopedRemoteRoot((string) ($config['remote_root'] ?? '/khub-backups')),
        ]);
        $adapter->probe();
    }

    private function probeFtp(): void
    {
        $config = $this->config();
        $adapter = new FtpAdapter([
            'host' => $config['host'] ?? '',
            'username' => $config['username'] ?? '',
            'password' => $config['password'] ?? null,
            'port' => (int) ($config['port'] ?? 21),
            'ssl' => (bool) ($config['ssl'] ?? false),
            'passive' => (bool) ($config['passive'] ?? true),
            'root' => $this->storage->siteScopedRemoteRoot((string) ($config['remote_root'] ?? '/khub-backups')),
            'path_prefix' => $this->storage->siteScopedPrefix((string) ($config['remote_prefix'] ?? 'sql-backups')),
        ]);
        $adapter->probe();
    }

    private function recordUploadResult(string $status, string $message, ?string $remotePath = null): void
    {
        $settings = HubStorageSetting::current();
        if (! $settings->getKey()) {
            return;
        }

        $config = $this->config();
        $config['last_upload_at'] = now()->toIso8601String();
        $config['last_upload_status'] = $status;
        $config['last_upload_message'] = $message;
        if ($remotePath !== null) {
            $config['last_upload_path'] = $remotePath;
        }

        $settings->update(['offsite_backup_config' => $config]);
        $this->storage->forgetSettingsCache();
    }
}
