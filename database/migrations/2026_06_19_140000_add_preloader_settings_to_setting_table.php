<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreloaderSettingsToSettingTable extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'preloader_enabled')) {
                $after = Schema::hasColumn('setting', 'search_pagination_mode')
                    ? 'search_pagination_mode'
                    : 'enable_ai_search';
                $table->boolean('preloader_enabled')->default(true)->after($after);
            }
            if (! Schema::hasColumn('setting', 'preloader_text')) {
                $table->string('preloader_text', 120)->default('Loading')->after('preloader_enabled');
            }
            if (! Schema::hasColumn('setting', 'preloader_min_seconds')) {
                $table->unsignedTinyInteger('preloader_min_seconds')->default(3)->after('preloader_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach (['preloader_min_seconds', 'preloader_text', 'preloader_enabled'] as $col) {
                if (Schema::hasColumn('setting', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
