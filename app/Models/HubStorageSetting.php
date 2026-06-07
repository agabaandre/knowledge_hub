<?php

namespace App\Models;

use App\Support\HubSiteIdentifier;
use Illuminate\Database\Eloquent\Model;

class HubStorageSetting extends Model
{
    protected $fillable = [
        'files_driver',
        'site_storage_id',
        'local_files_root',
        'sql_backup_root',
        'cloud_config',
        'offsite_backup_config',
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
        'offsite_backup_config' => 'array',
        'auto_sql_backup' => 'boolean',
        'sql_backup_retention_days' => 'integer',
        'last_sql_backup_at' => 'datetime',
    ];

    private static ?self $currentCache = null;

    protected static function booted(): void
    {
        static::saved(static function () {
            self::$currentCache = null;
        });
    }

    public static function forgetCache(): void
    {
        self::$currentCache = null;
    }

    public static function current(): self
    {
        if (self::$currentCache !== null) {
            return self::$currentCache;
        }

        $record = static::query()->first();
        if ($record) {
            return self::$currentCache = $record;
        }

        $siteId = trim((string) env('HUB_SITE_ID', ''));
        $siteId = $siteId !== '' ? HubSiteIdentifier::sanitize($siteId) : HubSiteIdentifier::fromAppUrl();
        $paths = HubSiteIdentifier::defaultPaths($siteId);
        $filesRoot = trim((string) env('HUB_FILES_ROOT', ''));
        if ($filesRoot === '') {
            $filesRoot = $paths['files'];
        }
        $sqlRoot = trim((string) env('HUB_SQL_BACKUP_ROOT', ''));
        if ($sqlRoot === '') {
            $sqlRoot = $paths['sql_backups'];
        }

        return self::$currentCache = static::query()->create([
            'files_driver' => 'internal',
            'site_storage_id' => $siteId,
            'local_files_root' => $filesRoot,
            'sql_backup_root' => $sqlRoot,
            'auto_sql_backup' => true,
            'sql_backup_retention_days' => 30,
        ]);
    }
}
