<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('setting', 'nav_style')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_style', 20)->default('colored')->after('footer_logo_inverse');
            });
        }
        if (!Schema::hasColumn('setting', 'nav_link_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_link_color', 20)->nullable()->after('nav_style');
            });
        }
        if (!Schema::hasColumn('setting', 'nav_link_hover_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_link_hover_color', 20)->nullable()->after('nav_link_color');
            });
        }
        if (!Schema::hasColumn('setting', 'nav_link_active_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_link_active_color', 20)->nullable()->after('nav_link_hover_color');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'nav_style')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_style');
            });
        }
        if (Schema::hasColumn('setting', 'nav_link_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_link_color');
            });
        }
        if (Schema::hasColumn('setting', 'nav_link_hover_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_link_hover_color');
            });
        }
        if (Schema::hasColumn('setting', 'nav_link_active_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_link_active_color');
            });
        }
    }
};
