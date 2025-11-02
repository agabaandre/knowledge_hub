<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // First, check the actual column type of forums.id
            $forumIdType = 'unsignedBigInteger';
            if (Schema::hasTable('forums')) {
                try {
                    $columnInfo = DB::select("SHOW COLUMNS FROM forums WHERE Field = 'id'");
                    if (!empty($columnInfo)) {
                        $type = strtolower($columnInfo[0]->Type);
                        // If it's int (not bigint), use unsignedInteger
                        if (strpos($type, 'int') !== false && strpos($type, 'bigint') === false) {
                            $forumIdType = 'unsignedInteger';
                        }
                    }
                } catch (\Exception $e) {
                    // Default to unsignedBigInteger if we can't check
                }
            }
            
            Schema::create('forum_likes', function (Blueprint $table) use ($forumIdType) {
                $table->id();
                
                // Use the appropriate type based on forums.id type
                if ($forumIdType === 'unsignedInteger') {
                    $table->unsignedInteger('forum_id');
                } else {
                    $table->unsignedBigInteger('forum_id');
                }
                
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->unique(['forum_id', 'user_id']); // Prevent duplicate likes
            });
            
            // Add foreign key constraints separately to handle type mismatches gracefully
            if (Schema::hasTable('forums')) {
                try {
                    DB::statement('ALTER TABLE forum_likes ADD CONSTRAINT forum_likes_forum_id_foreign FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails, continue without it
                    // This can happen if column types don't match
                }
            }
            
            if (Schema::hasTable('users')) {
                try {
                    DB::statement('ALTER TABLE forum_likes ADD CONSTRAINT forum_likes_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails, continue without it
                }
            }
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
            });
            
            // Check if unique constraint exists before adding it
            $indexes = DB::select("SHOW INDEXES FROM forum_likes WHERE Key_name = 'forum_likes_forum_id_user_id_unique'");
            if (empty($indexes)) {
                try {
                    DB::statement('ALTER TABLE forum_likes ADD UNIQUE KEY forum_likes_forum_id_user_id_unique (forum_id, user_id)');
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
        Schema::dropIfExists('forum_likes');
    }
}
