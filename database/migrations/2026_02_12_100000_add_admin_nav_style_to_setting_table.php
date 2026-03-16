<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('setting', 'admin_nav_style')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('admin_nav_style', 20)->default('colored')->after('nav_link_active_color');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'admin_nav_style')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('admin_nav_style');
            });
        }
    }
};
