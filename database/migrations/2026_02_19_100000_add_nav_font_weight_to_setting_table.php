<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('setting', 'nav_font_weight')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('nav_font_weight', 10)->nullable()->default('500')->after('nav_link_active_color');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'nav_font_weight')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('nav_font_weight');
            });
        }
    }
};
