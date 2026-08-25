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
                if (! Schema::hasColumn('setting', 'frontend_theme')) {
                    $table->string('frontend_theme', 64)->default('university');
                }
                if (! Schema::hasColumn('setting', 'frontend_theme_packs')) {
                    $table->json('frontend_theme_packs')->nullable();
                }
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'frontend_theme'],
                [
                    'group_name' => 'Appearance',
                    'subgroup_name' => 'Frontend',
                    'sort_order' => 20,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (Schema::hasColumn('setting', 'frontend_theme_packs')) {
                    $table->dropColumn('frontend_theme_packs');
                }
                if (Schema::hasColumn('setting', 'frontend_theme')) {
                    $table->dropColumn('frontend_theme');
                }
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->where('setting_key', 'frontend_theme')->delete();
        }
    }
};
