<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteStorageIdToHubStorageSettingsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        Schema::table('hub_storage_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('hub_storage_settings', 'site_storage_id')) {
                $table->string('site_storage_id', 128)->nullable()->after('files_driver');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('hub_storage_settings')) {
            return;
        }

        Schema::table('hub_storage_settings', function (Blueprint $table) {
            if (Schema::hasColumn('hub_storage_settings', 'site_storage_id')) {
                $table->dropColumn('site_storage_id');
            }
        });
    }
}
