<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'nav_font_size')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_font_size', 10)->nullable()->after('admin_body_font_size');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'nav_font_size')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_font_size');
            });
        }
    }
};
