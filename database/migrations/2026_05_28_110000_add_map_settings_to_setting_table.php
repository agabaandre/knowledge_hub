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
            if (! Schema::hasColumn('setting', 'africa_map_version')) {
                $table->string('africa_map_version', 120)->nullable()->after('nav_font_size');
            }
            if (! Schema::hasColumn('setting', 'africa_map_custom_versions')) {
                $table->text('africa_map_custom_versions')->nullable()->after('africa_map_version');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'africa_map_custom_versions')) {
                $table->dropColumn('africa_map_custom_versions');
            }
            if (Schema::hasColumn('setting', 'africa_map_version')) {
                $table->dropColumn('africa_map_version');
            }
        });
    }
};
