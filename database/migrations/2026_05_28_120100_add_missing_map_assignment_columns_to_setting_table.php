<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'africa_map_view_versions')) {
                if (Schema::hasColumn('setting', 'africa_map_custom_versions')) {
                    $table->text('africa_map_view_versions')->nullable()->after('africa_map_custom_versions');
                } else {
                    $table->text('africa_map_view_versions')->nullable();
                }
            }
            if (! Schema::hasColumn('setting', 'show_admin_units_map')) {
                if (Schema::hasColumn('setting', 'africa_map_view_versions')) {
                    $table->boolean('show_admin_units_map')->default(false)->after('africa_map_view_versions');
                } else {
                    $table->boolean('show_admin_units_map')->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            foreach (['show_admin_units_map', 'africa_map_view_versions'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
