<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('forum_likes')) {
            Schema::create('forum_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('forum_id'); // Changed from unsignedInteger to match forums.id (BIGINT)
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->unique(['forum_id', 'user_id']); // Prevent duplicate likes
                
                // Add foreign key constraints
                $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            // Table exists, ensure columns exist
            Schema::table('forum_likes', function (Blueprint $table) {
                if (!Schema::hasColumn('forum_likes', 'forum_id')) {
                    $table->unsignedBigInteger('forum_id')->after('id'); // Changed from unsignedInteger to match forums.id (BIGINT)
                    $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
                }
                if (!Schema::hasColumn('forum_likes', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('forum_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                // Try to add unique constraint if it doesn't exist
                try {
                    $table->unique(['forum_id', 'user_id']);
                } catch (\Exception $e) {
                    // Constraint might already exist
                }
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
        Schema::dropIfExists('forum_likes');
    }
}
