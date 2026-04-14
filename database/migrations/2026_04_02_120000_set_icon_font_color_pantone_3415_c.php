<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agenda 2063 / AU palette: Icon Font Color → PANTONE 3415 C (web #007749).
 */
return new class extends Migration
{
    public function up(): void
    {
        $hex = '#007749';

        if (Schema::hasColumn('setting', 'icon_font_color')) {
            DB::table('setting')->update(['icon_font_color' => $hex]);
        }

        if (Schema::hasTable('theme_settings')) {
            DB::table('theme_settings')->where('key', 'icon_font_color')->update(['value' => $hex]);
        }
    }

    public function down(): void
    {
        // Intentionally left blank: prior values were site-specific.
    }
};
