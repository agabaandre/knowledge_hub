<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureSqlBackupRetentionOnHubStorageSettings extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        if (! Schema::hasColumn('hub_storage_settings', 'sql_backup_retention_days')) {
            Schema::table('hub_storage_settings', function (Blueprint $table) {
                $table->unsignedSmallInteger('sql_backup_retention_days')->default(30)->after('auto_sql_backup');
            });
        }
    }

    public function down()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        if (Schema::hasColumn('hub_storage_settings', 'sql_backup_retention_days')) {
            Schema::table('hub_storage_settings', function (Blueprint $table) {
                $table->dropColumn('sql_backup_retention_days');
            });
        }
    }
}
