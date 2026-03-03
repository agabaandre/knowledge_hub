<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchShowForumsCommunitiesToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('setting', 'search_show_forums')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('search_show_forums')->default(true);
            });
        }
        if (!Schema::hasColumn('setting', 'search_show_communities')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('search_show_communities')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('setting', 'search_show_forums')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('search_show_forums');
            });
        }
        if (Schema::hasColumn('setting', 'search_show_communities')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('search_show_communities');
            });
        }
    }
}
