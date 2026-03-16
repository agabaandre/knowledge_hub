<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('setting', 'translate_button_filled')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('translate_button_filled')->default(true)->after('site_theme');
            });
        }
        if (!Schema::hasColumn('setting', 'translate_button_text_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->string('translate_button_text_color', 20)->default('#ffffff')->after('translate_button_filled');
            });
        }
        if (!Schema::hasColumn('setting', 'header_logo_inverse')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('header_logo_inverse')->default(false)->after('translate_button_text_color');
            });
        }
        if (!Schema::hasColumn('setting', 'footer_logo_inverse')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('footer_logo_inverse')->default(false)->after('header_logo_inverse');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'translate_button_filled')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('translate_button_filled');
            });
        }
        if (Schema::hasColumn('setting', 'translate_button_text_color')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('translate_button_text_color');
            });
        }
        if (Schema::hasColumn('setting', 'header_logo_inverse')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('header_logo_inverse');
            });
        }
        if (Schema::hasColumn('setting', 'footer_logo_inverse')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('footer_logo_inverse');
            });
        }
    }
};
