<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommunitiesPaginationModeToSettingTable extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'communities_pagination_mode')) {
                $after = Schema::hasColumn('setting', 'forums_pagination_mode')
                    ? 'forums_pagination_mode'
                    : (Schema::hasColumn('setting', 'search_pagination_mode')
                        ? 'search_pagination_mode'
                        : 'enable_ai_search');
                $table->string('communities_pagination_mode', 32)->default('infinite_scroll')->after($after);
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'communities_pagination_mode')) {
                $table->dropColumn('communities_pagination_mode');
            }
        });
    }
}
