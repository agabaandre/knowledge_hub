<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'partner_logo_max_height')) {
            Schema::table('setting', function (Blueprint $table) {
                $after = Schema::hasColumn('setting', 'show_partner_names') ? 'show_partner_names' : null;
                $column = $table->unsignedSmallInteger('partner_logo_max_height')->default(100);
                if ($after) {
                    $column->after($after);
                }
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'partner_logo_max_height'],
                [
                    'group_name' => 'Appearance',
                    'subgroup_name' => 'Branding',
                    'sort_order' => 14,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'partner_logo_max_height')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('partner_logo_max_height');
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->where('setting_key', 'partner_logo_max_height')->delete();
        }
    }
};
