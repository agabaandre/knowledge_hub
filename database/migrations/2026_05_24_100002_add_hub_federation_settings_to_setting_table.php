<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHubFederationSettingsToSettingTable extends Migration
{
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'admin_units_enabled')) {
                $table->boolean('admin_units_enabled')->nullable()->after('profile_reminder_day_of_month');
            }
            if (! Schema::hasColumn('setting', 'default_owner_country_id')) {
                $table->unsignedBigInteger('default_owner_country_id')->nullable()->after('admin_units_enabled');
            }
            if (! Schema::hasColumn('setting', 'default_owner_region_id')) {
                $table->unsignedBigInteger('default_owner_region_id')->nullable()->after('default_owner_country_id');
            }
            if (! Schema::hasColumn('setting', 'federation_api_token')) {
                $table->string('federation_api_token', 128)->nullable()->after('default_owner_region_id');
            }
        });
    }

    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach (['federation_api_token', 'default_owner_region_id', 'default_owner_country_id', 'admin_units_enabled'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
