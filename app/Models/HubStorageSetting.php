<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubStorageSetting extends Model
{
    protected $fillable = [
        'files_driver',
        'local_files_root',
        'sql_backup_root',
        'cloud_config',
        'auto_sql_backup',
        'sql_backup_retention_days',
        'last_sql_backup_at',
        'last_sql_backup_path',
        'migration_status',
        'migration_files_total',
        'migration_files_done',
        'migration_message',
    ];

    protected $casts = [
        'cloud_config' => 'array',
        'auto_sql_backup' => 'boolean',
        'last_sql_backup_at' => 'datetime',
    ];

    public static function current(): self
    {
        $record = static::query()->first();
        if ($record) {
            return $record;
        }

        $filesRoot = trim((string) env('HUB_FILES_ROOT', ''));
        if ($filesRoot === '') {
            $filesRoot = PHP_OS_FAMILY === 'Windows' ? 'C:\\khubdata\\files' : '/var/khubdata/files';
        }
        $sqlRoot = trim((string) env('HUB_SQL_BACKUP_ROOT', ''));
        if ($sqlRoot === '') {
            $sqlRoot = PHP_OS_FAMILY === 'Windows' ? 'C:\\khubdata\\backups\\sql' : '/var/khubdata/backups/sql';
        }

        return static::query()->create([
            'files_driver' => 'internal',
            'local_files_root' => $filesRoot,
            'sql_backup_root' => $sqlRoot,
            'auto_sql_backup' => true,
            'sql_backup_retention_days' => 30,
        ]);
    }
}
