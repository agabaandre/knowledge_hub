<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (! Schema::hasColumn('setting', 'spotlight_animation')) {
                    $table->string('spotlight_animation', 32)->default('none')->after('spotlight_overlay_opacity');
                }
                if (! Schema::hasColumn('setting', 'spotlight_animation_speed')) {
                    $table->string('spotlight_animation_speed', 16)->default('medium')->after('spotlight_animation');
                }
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            foreach ([
                ['spotlight_animation', 'Appearance', 'Homepage Hero', 37],
                ['spotlight_animation_speed', 'Appearance', 'Homepage Hero', 38],
            ] as [$key, $group, $subgroup, $order]) {
                DB::table('setting_key_groups')->updateOrInsert(
                    ['setting_key' => $key],
                    [
                        'group_name' => $group,
                        'subgroup_name' => $subgroup,
                        'sort_order' => $order,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (Schema::hasColumn('setting', 'spotlight_animation_speed')) {
                    $table->dropColumn('spotlight_animation_speed');
                }
                if (Schema::hasColumn('setting', 'spotlight_animation')) {
                    $table->dropColumn('spotlight_animation');
                }
            });
        }
    }
};
