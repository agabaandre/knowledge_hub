<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOffsiteBackupConfigToHubStorageSettings extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        Schema::table('hub_storage_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('hub_storage_settings', 'offsite_backup_config')) {
                $table->json('offsite_backup_config')->nullable()->after('cloud_config');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        Schema::table('hub_storage_settings', function (Blueprint $table) {
            if (Schema::hasColumn('hub_storage_settings', 'offsite_backup_config')) {
                $table->dropColumn('offsite_backup_config');
            }
        });
    }
}
