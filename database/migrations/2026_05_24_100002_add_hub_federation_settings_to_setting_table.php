<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHubFederationSettingsToSettingTable extends Migration
{
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            $after = $this->firstExistingColumn([
                'profile_reminder_day_of_month',
                'auto_profile_completion_reminder',
                'enable_ai_chat_prune',
                'enable_ai_search',
            ]);

            if (! Schema::hasColumn('setting', 'admin_units_enabled')) {
                $column = $table->boolean('admin_units_enabled')->nullable();
                if ($after) {
                    $column->after($after);
                }
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

    /**
     * @param  array<int, string>  $candidates
     */
    private function firstExistingColumn(array $candidates): ?string
    {
        foreach ($candidates as $column) {
            if (Schema::hasColumn('setting', $column)) {
                return $column;
            }
        }

        return null;
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
