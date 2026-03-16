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
                if (!Schema::hasColumn('setting', 'show_health_themes')) {
                    $t->boolean('show_health_themes')->default(true)->after('show_quotes');
                }
                if (!Schema::hasColumn('setting', 'section_title_health_themes')) {
                    $t->string('section_title_health_themes', 255)->nullable()->after('show_health_themes');
                }
                if (!Schema::hasColumn('setting', 'section_title_top_searches')) {
                    $t->string('section_title_top_searches', 255)->nullable()->after('section_title_health_themes');
                }
                if (!Schema::hasColumn('setting', 'section_title_recommended')) {
                    $t->string('section_title_recommended', 255)->nullable()->after('section_title_top_searches');
                }
                if (!Schema::hasColumn('setting', 'section_title_flagship_initiatives')) {
                    $t->string('section_title_flagship_initiatives', 255)->nullable()->after('section_title_recommended');
                }
                if (!Schema::hasColumn('setting', 'theme_card_opacity')) {
                    $t->string('theme_card_opacity', 10)->nullable()->after('section_title_flagship_initiatives');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (Schema::hasColumn('setting', 'show_health_themes')) $t->dropColumn('show_health_themes');
                if (Schema::hasColumn('setting', 'section_title_health_themes')) $t->dropColumn('section_title_health_themes');
                if (Schema::hasColumn('setting', 'section_title_top_searches')) $t->dropColumn('section_title_top_searches');
                if (Schema::hasColumn('setting', 'section_title_recommended')) $t->dropColumn('section_title_recommended');
                if (Schema::hasColumn('setting', 'section_title_flagship_initiatives')) $t->dropColumn('section_title_flagship_initiatives');
                if (Schema::hasColumn('setting', 'theme_card_opacity')) $t->dropColumn('theme_card_opacity');
            });
        }
    }
};
