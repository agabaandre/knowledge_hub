<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCentralHubSettingsToSettingTable extends Migration
{
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'central_hub_url')) {
                $table->string('central_hub_url', 500)->nullable()->after('federation_api_token');
            }
            if (! Schema::hasColumn('setting', 'central_hub_api_token')) {
                $table->string('central_hub_api_token', 255)->nullable()->after('central_hub_url');
            }
            if (! Schema::hasColumn('setting', 'central_hub_site_id')) {
                $table->string('central_hub_site_id', 128)->nullable()->after('central_hub_api_token');
            }
            if (! Schema::hasColumn('setting', 'central_hub_connected_at')) {
                $table->timestamp('central_hub_connected_at')->nullable()->after('central_hub_site_id');
            }
            if (! Schema::hasColumn('setting', 'central_metadata_synced_at')) {
                $table->timestamp('central_metadata_synced_at')->nullable()->after('central_hub_connected_at');
            }
        });
    }

    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach ([
                'central_metadata_synced_at',
                'central_hub_connected_at',
                'central_hub_site_id',
                'central_hub_api_token',
                'central_hub_url',
            ] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
