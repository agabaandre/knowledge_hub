<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'convert_office_uploads_to_pdf')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('convert_office_uploads_to_pdf')->default(true);
            });
        }

        if (Schema::hasTable('setting_key_groups') && Schema::hasColumn('setting', 'convert_office_uploads_to_pdf')) {
            DB::table('setting_key_groups')->insertOrIgnore([
                'setting_key' => 'convert_office_uploads_to_pdf',
                'group_name' => 'Advanced',
                'subgroup_name' => 'Uploads',
                'sort_order' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')->where('setting_key', 'convert_office_uploads_to_pdf')->delete();
        }

        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'convert_office_uploads_to_pdf')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('convert_office_uploads_to_pdf');
            });
        }
    }
};
