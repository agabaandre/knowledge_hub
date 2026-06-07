<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class HubEnvBackupService
{
    public function __construct(private HubStorageService $storage)
    {
    }

    public function backupRoot(): string
    {
        return $this->storage->sqlBackupRoot().DIRECTORY_SEPARATOR.'env';
    }

    /**
     * @return array{name: string, path: string, size: int}
     */
    public function runBackup(): array
    {
        $source = base_path('.env');
        if (! File::exists($source)) {
            throw new \RuntimeException('Application .env file was not found.');
        }

        $root = $this->backupRoot();
        File::ensureDirectoryExists($root, 0775, true);

        $name = 'env_'.now()->format('Y-m-d_His').'.env';
        $dest = $root.DIRECTORY_SEPARATOR.$name;
        File::copy($source, $dest);

        $this->pruneOldBackups();

        return [
            'name' => $name,
            'path' => $dest,
            'size' => (int) filesize($dest),
        ];
    }

    /**
     * @return list<array{name: string, path: string, size: int, created_at: int|null}>
     */
    public function listBackups(): array
    {
        $root = $this->backupRoot();
        if (! is_dir($root)) {
            return [];
        }

        $out = [];
        foreach (scandir($root) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..' || ! str_ends_with($entry, '.env')) {
                continue;
            }

            $path = $root.DIRECTORY_SEPARATOR.$entry;
            if (! is_file($path)) {
                continue;
            }

            $out[] = [
                'name' => $entry,
                'path' => $path,
                'size' => (int) filesize($path),
                'created_at' => filemtime($path) ?: null,
            ];
        }

        usort($out, fn ($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

        return $out;
    }

    public function resolveBackupPath(string $name): ?string
    {
        $name = basename($name);
        if ($name === '' || ! str_ends_with($name, '.env')) {
            return null;
        }

        $path = $this->backupRoot().DIRECTORY_SEPARATOR.$name;
        $rootReal = realpath($this->backupRoot());
        $fileReal = realpath($path);

        if ($rootReal === false || $fileReal === false || ! str_starts_with($fileReal, $rootReal)) {
            return null;
        }

        return is_file($fileReal) ? $fileReal : null;
    }

    private function pruneOldBackups(): void
    {
        $settings = $this->storage->settings();
        $retention = (int) ($settings->sql_backup_retention_days ?? 30);
        $cutoff = now()->subDays($retention)->timestamp;

        foreach ($this->listBackups() as $backup) {
            if (($backup['created_at'] ?? 0) < $cutoff) {
                File::delete($backup['path']);
            }
        }
    }
}
