<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HubDatabaseBackupService
{
    public function __construct(private HubStorageService $storage)
    {
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function backupTableGroups(): array
    {
        return config('hub_storage.backup_table_groups', []);
    }

    /**
     * Ordered flat list of tables to back up / restore.
     *
     * @return list<string>
     */
    public function backupTables(): array
    {
        $tables = [];
        foreach ($this->backupTableGroups() as $groupTables) {
            foreach (array_keys($groupTables) as $table) {
                $tables[] = $table;
            }
        }

        return $tables;
    }

    /**
     * @return list<string>
     */
    public function tablesInBackupDirectory(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $available = [];
        foreach ($this->backupTables() as $table) {
            if (File::exists($directory.DIRECTORY_SEPARATOR.$table.'.sql')) {
                $available[] = $table;
            }
        }

        return $available;
    }

    /**
     * @return array{path: string, tables: int, incremental: bool}
     */
    public function runBackup(bool $incremental = true): array
    {
        $root = $this->storage->sqlBackupRoot();
        File::ensureDirectoryExists($root, 0775, true);

        $stamp = now()->format('Y-m-d_His');
        $dir = $root.DIRECTORY_SEPARATOR.($incremental ? 'incremental' : 'full').DIRECTORY_SEPARATOR.$stamp;
        File::ensureDirectoryExists($dir, 0775, true);

        $manifestPath = $root.DIRECTORY_SEPARATOR.'manifest.json';
        $manifest = File::exists($manifestPath)
            ? json_decode((string) File::get($manifestPath), true) ?: []
            : [];

        $tables = $this->backupTables();
        $exported = 0;

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if ($this->exportTable($table, $dir, $incremental, $manifest)) {
                $exported++;
            }
        }

        if (! $incremental) {
            $manifest['last_full_at'] = now()->toIso8601String();
        }
        $manifest['last_incremental_at'] = now()->toIso8601String();
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));

        $settings = $this->storage->settings();
        $settings->update([
            'last_sql_backup_at' => now(),
            'last_sql_backup_path' => $dir,
        ]);

        $this->pruneOldBackups();

        $envBackup = null;
        try {
            $envBackup = app(HubEnvBackupService::class)->runBackup();
        } catch (\Throwable) {
            // SQL backup should succeed even when .env is missing or unreadable.
        }

        return [
            'path' => $dir,
            'tables' => $exported,
            'incremental' => $incremental,
            'env_backup' => $envBackup,
        ];
    }

    /**
     * @param  list<string>|null  $selectedTables  Tables to restore (must be a subset of backupTables()). Null = all present in backup.
     * @return array{restored: list<string>, skipped: list<string>, errors: list<string>, missing: list<string>}
     */
    public function restoreFromDirectory(string $directory, bool $onlyEmptyTables = true, ?array $selectedTables = null): array
    {
        $result = ['restored' => [], 'skipped' => [], 'errors' => [], 'missing' => []];
        if (! is_dir($directory)) {
            $result['errors'][] = 'Backup directory not found.';

            return $result;
        }

        $allowed = array_flip($this->backupTables());
        $tables = $selectedTables ?? $this->tablesInBackupDirectory($directory);

        foreach ($tables as $table) {
            if (! isset($allowed[$table])) {
                $result['errors'][] = "Table {$table} is not allowed for restore.";

                continue;
            }

            $file = $directory.DIRECTORY_SEPARATOR.$table.'.sql';
            if (! File::exists($file)) {
                $result['missing'][] = $table;

                continue;
            }
            if (! Schema::hasTable($table)) {
                $result['errors'][] = "Table {$table} does not exist in this database.";

                continue;
            }

            $count = (int) DB::table($table)->count();
            if ($count > 0 && $onlyEmptyTables) {
                $result['skipped'][] = $table;

                continue;
            }

            if ($count > 0) {
                DB::table($table)->truncate();
            }

            try {
                $this->runSqlFile($file);
                $result['restored'][] = $table;
            } catch (\Throwable $e) {
                $result['errors'][] = $table.': '.$e->getMessage();
            }
        }

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listBackups(): array
    {
        $root = $this->storage->sqlBackupRoot();
        $out = [];
        foreach (['full', 'incremental'] as $type) {
            $typeDir = $root.DIRECTORY_SEPARATOR.$type;
            if (! is_dir($typeDir)) {
                continue;
            }
            foreach (scandir($typeDir) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $path = $typeDir.DIRECTORY_SEPARATOR.$entry;
                if (! is_dir($path)) {
                    continue;
                }
                $out[] = [
                    'type' => $type,
                    'name' => $entry,
                    'path' => $path,
                    'created_at' => filemtime($path) ?: null,
                ];
            }
        }

        usort($out, fn ($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

        return $out;
    }

    private function exportTable(string $table, string $dir, bool $incremental, array &$manifest): bool
    {
        $query = DB::table($table);
        $manifestKey = $table;
        $sinceId = 0;

        if ($incremental && isset($manifest['tables'][$manifestKey]['last_id'])) {
            $sinceId = (int) $manifest['tables'][$manifestKey]['last_id'];
            if (Schema::hasColumn($table, 'id')) {
                $query->where('id', '>', $sinceId);
            }
        }

        $rows = $query->orderBy(Schema::hasColumn($table, 'id') ? 'id' : DB::raw('1'))->get();
        if ($rows->isEmpty() && $incremental) {
            return false;
        }

        $sql = "-- Hub backup {$table} ".now()->toIso8601String()."\n";
        $maxId = $sinceId;

        foreach ($rows as $row) {
            $data = (array) $row;
            if (isset($data['id'])) {
                $maxId = max($maxId, (int) $data['id']);
            }
            $columns = array_map(fn ($c) => '`'.$c.'`', array_keys($data));
            $values = array_map(function ($value) {
                if ($value === null) {
                    return 'NULL';
                }
                if (is_bool($value)) {
                    return $value ? '1' : '0';
                }
                if (is_int($value) || is_float($value)) {
                    return (string) $value;
                }

                return "'".str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $value)."'";
            }, array_values($data));

            $sql .= 'INSERT INTO `'.$table.'` ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).');'."\n";
        }

        File::put($dir.DIRECTORY_SEPARATOR.$table.'.sql', $sql);

        if (Schema::hasColumn($table, 'id')) {
            $manifest['tables'][$manifestKey] = [
                'last_id' => $maxId,
                'exported_at' => now()->toIso8601String(),
            ];
        }

        return true;
    }

    private function runSqlFile(string $file): void
    {
        $sql = (string) File::get($file);
        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql) ?: []));
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($statements as $statement) {
            if ($statement === '' || Str::startsWith($statement, '--')) {
                continue;
            }
            DB::unprepared($statement);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function pruneOldBackups(): void
    {
        $days = (int) ($this->storage->settings()->sql_backup_retention_days ?? 30);
        if ($days <= 0) {
            return;
        }
        $cutoff = now()->subDays($days)->getTimestamp();
        foreach ($this->listBackups() as $backup) {
            if (($backup['created_at'] ?? 0) < $cutoff) {
                File::deleteDirectory($backup['path']);
            }
        }
    }
}
