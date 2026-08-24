<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'show_partner_names')) {
            Schema::table('setting', function (Blueprint $table) {
                $after = Schema::hasColumn('setting', 'partner_logos') ? 'partner_logos' : null;
                $column = $table->boolean('show_partner_names')->default(false);
                if ($after) {
                    $column->after($after);
                }
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'show_partner_names'],
                [
                    'group_name' => 'Appearance',
                    'subgroup_name' => 'Branding',
                    'sort_order' => 13,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'show_partner_names')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('show_partner_names');
            });
        }

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->where('setting_key', 'show_partner_names')->delete();
        }
    }
};
