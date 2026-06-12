<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchPaginationModeToSettingTable extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'search_pagination_mode')) {
                $table->string('search_pagination_mode', 32)->default('pagination')->after('enable_ai_search');
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'search_pagination_mode')) {
                $table->dropColumn('search_pagination_mode');
            }
        });
    }
}
