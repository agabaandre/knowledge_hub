<?php

namespace App\Services;

use App\Support\MetricsCache;
use App\Support\QueueHealth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class HubStorageMetricsService
{
    private const CACHE_TTL = 300;

    /** @var array<string, string> */
    private const DEPLOYMENT_LABELS = [
        'docker' => 'Docker',
        'bare_metal' => 'Linux / VPS',
        'subdirectory' => 'Subdirectory (Apache/Nginx alias)',
        'local_dev' => 'Local development',
        'windows' => 'Windows server',
        'custom' => 'Custom',
    ];

    /**
     * @return array<string, mixed>
     */
    public function snapshot(HubStorageService $storage, bool $fresh = false): array
    {
        $cacheKey = 'hub_storage_metrics_'.$storage->siteStorageId();

        if ($fresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, fn () => $this->collect($storage));
    }

    public static function formatBytes(?int $bytes, int $precision = 1): string
    {
        if ($bytes === null || $bytes < 0) {
            return '—';
        }

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB', 'PB'];
        $value = (float) $bytes;

        foreach ($units as $unit) {
            $value /= 1024;
            if ($value < 1024) {
                return number_format($value, $value >= 100 ? 0 : $precision).' '.$unit;
            }
        }

        return number_format($value, $precision).' PB';
    }

    /**
     * @return array<string, mixed>
     */
    protected function collect(HubStorageService $storage): array
    {
        $recommended = $storage->recommendedPaths();
        $filesRoot = $storage->filesRoot();
        $configuredRoot = $storage->configuredInternalRoot();
        $legacyRoot = $storage->legacyInternalRoot();
        $sqlBackupRoot = $storage->sqlBackupRoot();
        $hostSiteRoot = $recommended['site_root'];

        $legacySize = null;
        if ($storage->pathHasHubUploads($legacyRoot)
            && realpath($legacyRoot) !== realpath($filesRoot)) {
            $legacySize = $this->directorySize($legacyRoot);
        }

        $hostDisk = $this->diskStatsForPath($hostSiteRoot);
        $filesDisk = $this->diskStatsForPath($filesRoot);

        return [
            'collected_at' => now()->toIso8601String(),
            'cache_ttl_seconds' => self::CACHE_TTL,
            'disk' => [
                'host_site' => $hostDisk,
                'files_mount' => $filesDisk,
            ],
            'sizes' => [
                'uploads' => $this->directorySize($filesRoot),
                'configured_uploads' => $configuredRoot !== $filesRoot
                    ? $this->directorySize($configuredRoot)
                    : null,
                'sql_backups' => $this->directorySize($sqlBackupRoot),
                'legacy_uploads' => $legacySize,
                'app_storage' => $this->directorySize(storage_path()),
            ],
            'memory' => $this->memorySnapshot(),
            'load' => $this->loadSnapshot(),
            'runtime' => $this->runtimeSnapshot(),
            'deployment' => $this->deploymentSnapshot(),
            'database' => $this->databaseSnapshot(),
            'stack' => $this->stackSnapshot($storage),
            'queue' => QueueHealth::snapshot(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function runtimeSnapshot(): array
    {
        $webServer = null;
        if (app()->runningInConsole()) {
            $webServer = 'CLI';
        } elseif (! empty($_SERVER['SERVER_SOFTWARE'])) {
            $webServer = (string) $_SERVER['SERVER_SOFTWARE'];
        }

        return [
            'php_version' => PHP_VERSION,
            'php_major' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS_FAMILY,
            'os_kernel' => php_uname('s').' '.php_uname('r'),
            'sapi' => php_sapi_name(),
            'timezone' => config('app.timezone'),
            'web_server' => $webServer,
            'app_env' => config('app.env'),
            'app_debug' => (bool) config('app.debug'),
            'app_url' => rtrim((string) config('app.url'), '/'),
            'limits' => [
                'memory_limit' => ini_get('memory_limit') ?: '—',
                'max_execution_time' => ini_get('max_execution_time') ?: '—',
                'upload_max_filesize' => ini_get('upload_max_filesize') ?: '—',
                'post_max_size' => ini_get('post_max_size') ?: '—',
                'max_input_vars' => ini_get('max_input_vars') ?: '—',
            ],
            'extensions' => [
                'opcache' => extension_loaded('Zend OPcache') || extension_loaded('opcache'),
                'redis' => extension_loaded('redis'),
                'intl' => extension_loaded('intl'),
                'gd' => extension_loaded('gd'),
                'imagick' => extension_loaded('imagick'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function deploymentSnapshot(): array
    {
        $override = trim((string) env('HUB_DEPLOYMENT_TYPE', ''));
        if ($override !== '') {
            return [
                'type' => 'custom',
                'label' => self::DEPLOYMENT_LABELS['custom'],
                'custom_label' => $override,
                'detected' => false,
                'hints' => [],
            ];
        }

        $hints = [];
        $inDocker = file_exists('/.dockerenv') || filter_var(env('DOCKER', false), FILTER_VALIDATE_BOOLEAN);
        if ($inDocker) {
            $hints[] = '/.dockerenv or DOCKER=true';

            return [
                'type' => 'docker',
                'label' => self::DEPLOYMENT_LABELS['docker'],
                'detected' => true,
                'hints' => $hints,
            ];
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return [
                'type' => 'windows',
                'label' => self::DEPLOYMENT_LABELS['windows'],
                'detected' => true,
                'hints' => ['PHP_OS_FAMILY=Windows'],
            ];
        }

        $appUrl = (string) config('app.url');
        $parsed = parse_url($appUrl) ?: [];
        $host = strtolower((string) ($parsed['host'] ?? ''));
        $path = trim((string) ($parsed['path'] ?? ''), '/');

        if ($path !== '') {
            $hints[] = 'APP_URL path: /'.$path;

            return [
                'type' => 'subdirectory',
                'label' => self::DEPLOYMENT_LABELS['subdirectory'],
                'url_path' => '/'.$path,
                'detected' => true,
                'hints' => $hints,
            ];
        }

        if (in_array(config('app.env'), ['local', 'development'], true)
            && in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            $hints[] = 'APP_ENV='.config('app.env');

            return [
                'type' => 'local_dev',
                'label' => self::DEPLOYMENT_LABELS['local_dev'],
                'detected' => true,
                'hints' => $hints,
            ];
        }

        return [
            'type' => 'bare_metal',
            'label' => self::DEPLOYMENT_LABELS['bare_metal'],
            'detected' => true,
            'hints' => ['Production-style root URL'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function databaseSnapshot(): array
    {
        $driver = (string) config('database.default', 'mysql');
        $connection = config('database.connections.'.$driver, []);
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '');
        $database = (string) ($connection['database'] ?? '');

        $base = [
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'connected' => false,
            'version' => null,
            'version_short' => null,
            'size_bytes' => null,
            'size_human' => '—',
            'tables' => null,
            'error' => null,
        ];

        try {
            DB::connection()->getPdo();
            $base['connected'] = true;

            if ($driver === 'mysql') {
                $versionRow = DB::selectOne('SELECT VERSION() AS version');
                $version = (string) ($versionRow->version ?? '');
                $base['version'] = $version;
                $base['version_short'] = preg_match('/^[\d.]+/', $version, $m) ? $m[0] : $version;

                if ($database !== '') {
                    $sizeRow = DB::selectOne(
                        'SELECT COALESCE(SUM(data_length + index_length), 0) AS size
                         FROM information_schema.TABLES
                         WHERE table_schema = ?',
                        [$database]
                    );
                    $size = (int) ($sizeRow->size ?? 0);
                    $base['size_bytes'] = $size;
                    $base['size_human'] = self::formatBytes($size);

                    $tablesRow = DB::selectOne(
                        'SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE table_schema = ?',
                        [$database]
                    );
                    $base['tables'] = (int) ($tablesRow->cnt ?? 0);
                }
            } elseif ($driver === 'pgsql') {
                $versionRow = DB::selectOne('SELECT version() AS version');
                $base['version'] = (string) ($versionRow->version ?? '');
                $base['version_short'] = $base['version'];
            } elseif ($driver === 'sqlite') {
                $sqliteVersion = PHP_VERSION_ID >= 80300 && class_exists(\SQLite3::class)
                    ? (\SQLite3::version()['versionString'] ?? '')
                    : '';
                $base['version'] = $sqliteVersion !== '' ? 'SQLite '.$sqliteVersion : 'SQLite';
                $base['version_short'] = $base['version'];
                if (isset($connection['database']) && is_file($connection['database'])) {
                    $size = filesize($connection['database']);
                    $base['size_bytes'] = $size;
                    $base['size_human'] = self::formatBytes($size !== false ? (int) $size : null);
                }
            }
        } catch (\Throwable $e) {
            $base['error'] = $e->getMessage();
        }

        return $base;
    }

    /**
     * @return array<string, mixed>
     */
    protected function stackSnapshot(HubStorageService $storage): array
    {
        $cacheDriver = (string) config('cache.default', 'file');
        $sessionDriver = (string) config('session.driver', 'file');
        $queueDriver = (string) config('queue.default', 'sync');
        $scoutDriver = (string) config('scout.driver', 'collection');
        $filesystemDefault = (string) config('filesystems.default', 'local');

        $redisOk = null;
        if ($cacheDriver === 'redis' || $queueDriver === 'redis' || $sessionDriver === 'redis') {
            try {
                Redis::connection()->ping();
                $redisOk = true;
            } catch (\Throwable) {
                $redisOk = false;
            }
        }

        $meilisearchOk = null;
        $meilisearchHost = trim((string) config('scout.meilisearch.host', env('MEILISEARCH_HOST', '')));
        if ($scoutDriver === 'meilisearch' && $meilisearchHost !== '') {
            try {
                $response = Http::timeout(2)->get(rtrim($meilisearchHost, '/').'/health');
                $meilisearchOk = $response->successful();
            } catch (\Throwable) {
                $meilisearchOk = false;
            }
        }

        return [
            'cache_driver' => $cacheDriver,
            'session_driver' => $sessionDriver,
            'queue_connection' => $queueDriver,
            'scout_driver' => $scoutDriver,
            'filesystem_default' => $filesystemDefault,
            'redis_available' => MetricsCache::redisAvailable(),
            'redis_ok' => $redisOk,
            'meilisearch_host' => $meilisearchHost ?: null,
            'meilisearch_ok' => $meilisearchOk,
            'hub_files_driver' => $storage->settings()->files_driver ?? 'internal',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function diskStatsForPath(string $path): ?array
    {
        $path = rtrim($path, '/\\');
        if ($path === '') {
            return null;
        }

        $probe = $path;
        while ($probe !== '' && ! is_dir($probe)) {
            $parent = dirname($probe);
            if ($parent === $probe) {
                break;
            }
            $probe = $parent;
        }

        if ($probe === '' || ! is_dir($probe)) {
            return [
                'path' => $path,
                'probe_path' => $probe,
                'available' => false,
            ];
        }

        $free = @disk_free_space($probe);
        $total = @disk_total_space($probe);

        if ($free === false || $total === false || $total <= 0) {
            return [
                'path' => $path,
                'probe_path' => $probe,
                'available' => false,
            ];
        }

        $free = (int) $free;
        $total = (int) $total;
        $used = max(0, $total - $free);
        $usedPercent = round(($used / $total) * 100, 1);

        return [
            'path' => $path,
            'probe_path' => $probe,
            'available' => true,
            'total_bytes' => $total,
            'free_bytes' => $free,
            'used_bytes' => $used,
            'used_percent' => $usedPercent,
            'total_human' => self::formatBytes($total),
            'free_human' => self::formatBytes($free),
            'used_human' => self::formatBytes($used),
            'status' => $this->usageStatus($usedPercent),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function directorySize(string $path): ?array
    {
        if (! is_dir($path)) {
            return null;
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            $escaped = escapeshellarg($path);
            $output = [];
            $code = 1;
            @exec("du -sk {$escaped} 2>/dev/null", $output, $code);
            if ($code === 0 && isset($output[0])) {
                $parts = preg_split('/\s+/', trim($output[0]));
                $kb = (int) ($parts[0] ?? 0);
                $bytes = $kb * 1024;

                return [
                    'path' => $path,
                    'bytes' => $bytes,
                    'human' => self::formatBytes($bytes),
                    'method' => 'du',
                ];
            }
        }

        return $this->directorySizeWalk($path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function directorySizeWalk(string $path): array
    {
        $bytes = 0;
        $files = 0;
        $maxFiles = 50000;

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $bytes += (int) $fileInfo->getSize();
                    $files++;
                    if ($files >= $maxFiles) {
                        break;
                    }
                }
            }
        } catch (\Throwable) {
            return [
                'path' => $path,
                'bytes' => null,
                'human' => '—',
                'method' => 'unavailable',
            ];
        }

        return [
            'path' => $path,
            'bytes' => $bytes,
            'human' => self::formatBytes($bytes).($files >= $maxFiles ? '+' : ''),
            'method' => 'walk',
            'file_count' => $files,
            'truncated' => $files >= $maxFiles,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function memorySnapshot(): array
    {
        $phpUsed = memory_get_usage(true);
        $phpPeak = memory_get_peak_usage(true);
        $limit = ini_get('memory_limit');
        $limitBytes = $this->parseIniBytes($limit);

        $system = $this->systemMemorySnapshot();

        return [
            'php' => [
                'used_bytes' => $phpUsed,
                'peak_bytes' => $phpPeak,
                'limit_bytes' => $limitBytes,
                'used_human' => self::formatBytes($phpUsed),
                'peak_human' => self::formatBytes($phpPeak),
                'limit_human' => $limitBytes !== null ? self::formatBytes($limitBytes) : (string) $limit,
                'used_percent' => $limitBytes ? round(($phpUsed / $limitBytes) * 100, 1) : null,
                'status' => $limitBytes ? $this->usageStatus(($phpUsed / $limitBytes) * 100) : 'info',
            ],
            'system' => $system,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function systemMemorySnapshot(): ?array
    {
        if (! is_readable('/proc/meminfo')) {
            return null;
        }

        $total = null;
        $available = null;

        foreach (file('/proc/meminfo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with($line, 'MemTotal:')) {
                $total = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT) * 1024;
            } elseif (str_starts_with($line, 'MemAvailable:')) {
                $available = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT) * 1024;
            }
        }

        if ($total === null || $available === null || $total <= 0) {
            return null;
        }

        $used = max(0, $total - $available);
        $usedPercent = round(($used / $total) * 100, 1);

        return [
            'total_bytes' => $total,
            'available_bytes' => $available,
            'used_bytes' => $used,
            'used_percent' => $usedPercent,
            'total_human' => self::formatBytes($total),
            'available_human' => self::formatBytes($available),
            'used_human' => self::formatBytes($used),
            'status' => $this->usageStatus($usedPercent),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function loadSnapshot(): ?array
    {
        if (! function_exists('sys_getloadavg')) {
            return null;
        }

        $loads = sys_getloadavg();
        if ($loads === false) {
            return null;
        }

        return [
            '1m' => round($loads[0], 2),
            '5m' => round($loads[1], 2),
            '15m' => round($loads[2], 2),
        ];
    }

    protected function parseIniBytes(string|false $value): ?int
    {
        if ($value === false || $value === '' || $value === '-1') {
            return null;
        }

        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }

    protected function usageStatus(float $usedPercent): string
    {
        if ($usedPercent >= 90) {
            return 'bad';
        }

        if ($usedPercent >= 75) {
            return 'warn';
        }

        return 'ok';
    }
}
