<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddFulltextIndexToForumsTableForSearch extends Migration
{
    /**
     * Run the migrations.
     * Adds FULLTEXT index for forum_title and forum_description for full-text search.
     *
     * @return void
     */
    public function up()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }
        $db = DB::connection()->getDatabaseName();
        $existing = DB::select(
            "SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'forums' AND INDEX_NAME = 'ft_forum_search' LIMIT 1",
            [$db]
        );
        if (!empty($existing)) {
            return;
        }
        DB::statement('ALTER TABLE forums ADD FULLTEXT INDEX ft_forum_search (forum_title, forum_description)');
        Cache::forget('forums_fulltext_index_available');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }
        DB::statement('ALTER TABLE forums DROP INDEX ft_forum_search');
    }
}
