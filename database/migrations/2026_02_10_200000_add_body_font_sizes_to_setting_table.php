<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (!Schema::hasColumn('setting', 'front_body_font_size')) {
                    $t->string('front_body_font_size', 10)->nullable()->after('default_font_color');
                }
                if (!Schema::hasColumn('setting', 'admin_body_font_size')) {
                    $t->string('admin_body_font_size', 10)->nullable()->after('front_body_font_size');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (Schema::hasColumn('setting', 'front_body_font_size')) {
                    $t->dropColumn('front_body_font_size');
                }
                if (Schema::hasColumn('setting', 'admin_body_font_size')) {
                    $t->dropColumn('admin_body_font_size');
                }
            });
        }
    }
};
