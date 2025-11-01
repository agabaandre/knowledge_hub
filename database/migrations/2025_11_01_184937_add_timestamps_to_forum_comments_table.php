<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTimestampsToForumCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_comments', function (Blueprint $table) {
            // Check if columns don't already exist
            if (!Schema::hasColumn('forum_comments', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('forum_comments', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Populate existing records with current timestamp if created_at is null
        // This ensures existing comments have a timestamp
        DB::statement("UPDATE forum_comments SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_comments', function (Blueprint $table) {
            if (Schema::hasColumn('forum_comments', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('forum_comments', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
}
