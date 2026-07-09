<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class StaffEcosystemStorageService
{
    /** @var class-string|null */
    private static ?string $staffStorageClass = null;

    public function enabled(): bool
    {
        return (bool) config('hub_storage.staff_ecosystem.enabled', true);
    }

    public function repoRoot(): string
    {
        return rtrim((string) config('hub_storage.staff_ecosystem.repo_root', ''), '/\\');
    }

    public function siteId(): string
    {
        $override = trim((string) config('hub_storage.staff_ecosystem.site_id', ''));
        if ($override !== '') {
            return $this->sanitizeSiteId($override);
        }

        if ($this->staffStorageAvailable()) {
            return call_user_func([self::$staffStorageClass, 'siteId'], (string) config('hub_storage.staff_ecosystem.base_url'));
        }

        return $this->siteIdFromUrl((string) config('hub_storage.staff_ecosystem.base_url', 'http://localhost/staff'));
    }

    public function dataRoot(): string
    {
        if ($this->staffStorageAvailable()) {
            return call_user_func([self::$staffStorageClass, 'hostDataRoot'], $this->siteId());
        }

        $base = PHP_OS_FAMILY === 'Windows'
            ? (string) config('hub_storage.staff_ecosystem.host_data_root_windows', 'C:\\staffdata')
            : (string) config('hub_storage.staff_ecosystem.host_data_root', '/var/staffdata');

        return rtrim($base, '/\\').DIRECTORY_SEPARATOR.$this->siteId();
    }

    public function filesBackupRoot(): string
    {
        $explicit = trim((string) config('hub_storage.staff_ecosystem.backup_root', ''));
        if ($explicit !== '') {
            return rtrim($explicit, '/\\');
        }

        if ($this->staffStorageAvailable()) {
            return call_user_func([self::$staffStorageClass, 'filesBackupRoot'], $this->siteId());
        }

        return $this->dataRoot().DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.'files';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function moduleDefinitions(): array
    {
        return (array) config('hub_storage.staff_ecosystem.modules', []);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function moduleMetrics(): array
    {
        $metrics = [];
        foreach ($this->moduleDefinitions() as $key => $definition) {
            $legacy = $this->legacyPath($key);
            $host = $this->hostPath($key);
            $legacyStats = $this->directoryStats($legacy);
            $hostStats = $this->directoryStats($host);

            $metrics[$key] = array_merge($definition, [
                'key' => $key,
                'legacy_path' => $legacy,
                'host_path' => $host,
                'legacy_files' => $legacyStats['files'],
                'legacy_bytes' => $legacyStats['bytes'],
                'host_files' => $hostStats['files'],
                'host_bytes' => $hostStats['bytes'],
                'needs_migration' => $this->needsMigration($key),
                'env_var' => $definition['env_root'] ?? '',
            ]);
        }

        return $metrics;
    }

    public function legacyPath(string $module): string
    {
        $definition = $this->moduleDefinitions()[$module] ?? null;
        if ($definition === null) {
            return '';
        }

        return $this->repoRoot().DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, (string) $definition['legacy_relative']);
    }

    public function hostPath(string $module): string
    {
        $definition = $this->moduleDefinitions()[$module] ?? null;
        if ($definition === null) {
            return '';
        }

        return $this->dataRoot().DIRECTORY_SEPARATOR.(string) ($definition['host_subdir'] ?? $module);
    }

    public function needsMigration(string $module): bool
    {
        $legacy = $this->legacyPath($module);
        $host = $this->hostPath($module);
        $legacyStats = $this->directoryStats($legacy);

        if ($legacyStats['files'] === 0) {
            return false;
        }

        $hostStats = $this->directoryStats($host);

        return $hostStats['files'] < $legacyStats['files'] || $hostStats['bytes'] < $legacyStats['bytes'];
    }

    /**
     * @return array{status: string, message: string, output: string}
     */
    public function runMigration(string $module): array
    {
        $definition = $this->moduleDefinitions()[$module] ?? null;
        if ($definition === null) {
            return ['status' => 'error', 'message' => 'Unknown module.', 'output' => ''];
        }

        $script = $this->repoRoot().DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, (string) $definition['migrate_script']);
        if (! is_file($script)) {
            return ['status' => 'error', 'message' => "Migration script not found: {$script}", 'output' => ''];
        }

        $env = array_merge($_ENV, [
            'STAFF_DATA_ROOT' => $this->dataRoot(),
            'STAFF_SITE_ID' => $this->siteId(),
            'BASE_URL' => (string) config('hub_storage.staff_ecosystem.base_url'),
        ]);

        $process = Process::fromShellCommandline('bash '.escapeshellarg($script), $this->repoRoot(), $env);
        $process->setTimeout(3600);
        $process->run();

        $output = trim($process->getOutput()."\n".$process->getErrorOutput());

        if (! $process->isSuccessful()) {
            return [
                'status' => 'error',
                'message' => "Migration failed for {$module}.",
                'output' => $output,
            ];
        }

        return [
            'status' => 'completed',
            'message' => "Migration completed for {$module}.",
            'output' => $output,
        ];
    }

    /**
     * @param  list<string>|null  $modules
     * @return array{status: string, message: string, path: string}
     */
    public function runFileBackup(?array $modules = null): array
    {
        $modules = $modules ?? array_keys($this->moduleDefinitions());
        $timestamp = now()->format('Y-m-d_His');
        $backupDir = $this->filesBackupRoot().DIRECTORY_SEPARATOR.$timestamp;
        File::ensureDirectoryExists($backupDir, 0775, true);

        $backedUp = 0;
        foreach ($modules as $module) {
            if (! isset($this->moduleDefinitions()[$module])) {
                continue;
            }
            $source = $this->hostPath($module);
            if (! is_dir($source)) {
                continue;
            }
            $target = $backupDir.DIRECTORY_SEPARATOR.$module;
            File::ensureDirectoryExists($target, 0775, true);
            $this->copyDirectory($source, $target);
            $backedUp++;
        }

        $this->pruneOldBackups();

        return [
            'status' => 'completed',
            'message' => "File backup created ({$backedUp} module(s)).",
            'path' => $backupDir,
        ];
    }

    /**
     * @return list<array{folder: string, path: string, bytes: int, created_at: string}>
     */
    public function listFileBackups(): array
    {
        $root = $this->filesBackupRoot();
        if (! is_dir($root)) {
            return [];
        }

        $backups = [];
        foreach (scandir($root) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $root.DIRECTORY_SEPARATOR.$entry;
            if (! is_dir($path)) {
                continue;
            }
            $backups[] = [
                'folder' => $entry,
                'path' => $path,
                'bytes' => $this->directoryStats($path)['bytes'],
                'created_at' => date('Y-m-d H:i:s', filemtime($path) ?: time()),
            ];
        }

        usort($backups, static fn (array $a, array $b): int => strcmp($b['folder'], $a['folder']));

        return $backups;
    }

    private function pruneOldBackups(): void
    {
        $retention = (int) config('hub_storage.staff_ecosystem.backup_retention_days', 30);
        $cutoff = now()->subDays($retention)->getTimestamp();

        foreach ($this->listFileBackups() as $backup) {
            $mtime = filemtime($backup['path']) ?: 0;
            if ($mtime > 0 && $mtime < $cutoff) {
                File::deleteDirectory($backup['path']);
            }
        }
    }

    /**
     * @return array{files: int, bytes: int}
     */
    private function directoryStats(string $path): array
    {
        if (! is_dir($path)) {
            return ['files' => 0, 'bytes' => 0];
        }

        $files = 0;
        $bytes = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $files++;
            $bytes += (int) $file->getSize();
        }

        return ['files' => $files, 'bytes' => $bytes];
    }

    private function copyDirectory(string $source, string $target): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relative = Str::after($item->getPathname(), $source.DIRECTORY_SEPARATOR);
            $dest = $target.DIRECTORY_SEPARATOR.$relative;
            if ($item->isDir()) {
                File::ensureDirectoryExists($dest, 0775, true);
            } else {
                File::ensureDirectoryExists(dirname($dest), 0775, true);
                if (! is_file($dest)) {
                    File::copy($item->getPathname(), $dest);
                }
            }
        }
    }

    private function staffStorageAvailable(): bool
    {
        if (self::$staffStorageClass !== null) {
            return true;
        }

        $shared = $this->repoRoot().DIRECTORY_SEPARATOR.'shared'.DIRECTORY_SEPARATOR.'StaffStorage.php';
        if (! is_file($shared)) {
            return false;
        }

        require_once $shared;
        self::$staffStorageClass = \Staff\Shared\StaffStorage::class;

        return class_exists(self::$staffStorageClass);
    }

    private function siteIdFromUrl(string $appUrl): string
    {
        $appUrl = trim($appUrl);
        if ($appUrl === '') {
            return 'default';
        }

        $parsed = parse_url($appUrl);
        $host = strtolower((string) ($parsed['host'] ?? 'localhost'));
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        $port = isset($parsed['port']) ? (int) $parsed['port'] : null;
        $path = trim((string) ($parsed['path'] ?? ''), '/');
        $slug = implode('-', array_filter(explode('.', $host), static fn (string $p): bool => $p !== ''));

        if ($port && ! in_array($port, [80, 443], true)) {
            $slug .= '-'.$port;
        }
        if ($path !== '') {
            $pathSlug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($path)), '-');
            if ($pathSlug !== '') {
                $slug .= '-'.$pathSlug;
            }
        }

        return $this->sanitizeSiteId($slug ?: 'default');
    }

    private function sanitizeSiteId(string $id): string
    {
        $id = strtolower(trim($id));
        $id = preg_replace('/[^a-z0-9-]+/', '-', $id) ?? '';
        $id = preg_replace('/-+/', '-', $id) ?? '';

        return trim($id, '-') ?: 'default';
    }
}
