<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'partner_logos')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->text('partner_logos')->nullable();
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'partner_logos'],
                [
                    'group_name' => 'Appearance',
                    'subgroup_name' => 'Branding',
                    'sort_order' => 12,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'partner_logos')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('partner_logos');
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->where('setting_key', 'partner_logos')->delete();
        }
    }
};
