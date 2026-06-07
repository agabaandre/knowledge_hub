<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (! Schema::hasColumn('setting', 'map_topology_version')) {
                    $after = Schema::hasColumn('setting', 'show_admin_units_map')
                        ? 'show_admin_units_map'
                        : (Schema::hasColumn('setting', 'africa_map_view_versions') ? 'africa_map_view_versions' : null);
                    if ($after) {
                        $table->string('map_topology_version', 20)->nullable()->after($after);
                    } else {
                        $table->string('map_topology_version', 20)->nullable();
                    }
                }
                if (! Schema::hasColumn('setting', 'map_topology_version_checked_at')) {
                    $table->timestamp('map_topology_version_checked_at')->nullable()->after('map_topology_version');
                }
                if (! Schema::hasColumn('setting', 'map_topology_latest_version')) {
                    $table->string('map_topology_latest_version', 20)->nullable()->after('map_topology_version_checked_at');
                }
            });
        }

        if (! Schema::hasTable('map_topology_version_history')) {
            Schema::create('map_topology_version_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('from_version', 20);
                $table->string('to_version', 20);
                $table->string('action', 20);
                $table->json('assignments_snapshot')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('map_topology_version_history')) {
            Schema::dropIfExists('map_topology_version_history');
        }

        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                foreach (['map_topology_latest_version', 'map_topology_version_checked_at', 'map_topology_version'] as $column) {
                    if (Schema::hasColumn('setting', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
