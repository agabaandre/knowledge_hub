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
                if (! Schema::hasColumn('setting', 'spotlight_overlay_color')) {
                    $t->string('spotlight_overlay_color', 20)->nullable()->after('spotlight_banner');
                }
                if (! Schema::hasColumn('setting', 'spotlight_overlay_opacity')) {
                    $t->unsignedTinyInteger('spotlight_overlay_opacity')->default(35)->after('spotlight_overlay_color');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (Schema::hasColumn('setting', 'spotlight_overlay_opacity')) {
                    $t->dropColumn('spotlight_overlay_opacity');
                }
                if (Schema::hasColumn('setting', 'spotlight_overlay_color')) {
                    $t->dropColumn('spotlight_overlay_color');
                }
            });
        }
    }
};

