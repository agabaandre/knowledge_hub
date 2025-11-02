<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('forum_comment_likes')) {
            Schema::create('forum_comment_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('forum_comment_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->unique(['forum_comment_id', 'user_id']); // Prevent duplicate likes
                
                // Add foreign key constraints
                $table->foreign('forum_comment_id')->references('id')->on('forum_comments')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            // Table exists, ensure columns exist
            Schema::table('forum_comment_likes', function (Blueprint $table) {
                if (!Schema::hasColumn('forum_comment_likes', 'forum_comment_id')) {
                    $table->unsignedInteger('forum_comment_id')->after('id');
                    $table->foreign('forum_comment_id')->references('id')->on('forum_comments')->onDelete('cascade');
                }
                if (!Schema::hasColumn('forum_comment_likes', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('forum_comment_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                // Try to add unique constraint if it doesn't exist
                try {
                    $table->unique(['forum_comment_id', 'user_id']);
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
        Schema::dropIfExists('forum_comment_likes');
    }
}
