<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // First, check the actual column type of forum_comments.id
            $forumCommentIdType = 'unsignedBigInteger';
            if (Schema::hasTable('forum_comments')) {
                try {
                    $columnInfo = DB::select("SHOW COLUMNS FROM forum_comments WHERE Field = 'id'");
                    if (!empty($columnInfo)) {
                        $type = strtolower($columnInfo[0]->Type);
                        // If it's int (not bigint), use unsignedInteger
                        if (strpos($type, 'int') !== false && strpos($type, 'bigint') === false) {
                            $forumCommentIdType = 'unsignedInteger';
                        }
                    }
                } catch (\Exception $e) {
                    // Default to unsignedBigInteger if we can't check
                }
            }
            
            Schema::create('forum_comment_likes', function (Blueprint $table) use ($forumCommentIdType) {
                $table->id();
                
                // Use the appropriate type based on forum_comments.id type
                if ($forumCommentIdType === 'unsignedInteger') {
                    $table->unsignedInteger('forum_comment_id');
                } else {
                    $table->unsignedBigInteger('forum_comment_id');
                }
                
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->unique(['forum_comment_id', 'user_id']); // Prevent duplicate likes
            });
            
            // Add foreign key constraints separately to handle type mismatches gracefully
            if (Schema::hasTable('forum_comments')) {
                try {
                    DB::statement('ALTER TABLE forum_comment_likes ADD CONSTRAINT forum_comment_likes_forum_comment_id_foreign FOREIGN KEY (forum_comment_id) REFERENCES forum_comments(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails, continue without it
                    // This can happen if column types don't match
                }
            }
            
            if (Schema::hasTable('users')) {
                try {
                    DB::statement('ALTER TABLE forum_comment_likes ADD CONSTRAINT forum_comment_likes_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails, continue without it
                }
            }
        } else {
            // Table exists, ensure columns exist
            Schema::table('forum_comment_likes', function (Blueprint $table) {
                if (!Schema::hasColumn('forum_comment_likes', 'forum_comment_id')) {
                    $table->unsignedBigInteger('forum_comment_id')->after('id'); // Changed from unsignedInteger to match forum_comments.id (BIGINT)
                    $table->foreign('forum_comment_id')->references('id')->on('forum_comments')->onDelete('cascade');
                }
                if (!Schema::hasColumn('forum_comment_likes', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('forum_comment_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
            
            // Check if unique constraint exists before adding it
            $indexes = DB::select("SHOW INDEXES FROM forum_comment_likes WHERE Key_name = 'forum_comment_likes_forum_comment_id_user_id_unique'");
            if (empty($indexes)) {
                try {
                    DB::statement('ALTER TABLE forum_comment_likes ADD UNIQUE KEY forum_comment_likes_forum_comment_id_user_id_unique (forum_comment_id, user_id)');
                } catch (\Exception $e) {
                    // Constraint might already exist or other error
                }
            }
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
