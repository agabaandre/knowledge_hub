<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('forum_engagements')) {
            Schema::create('forum_engagements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->year('year');
                $table->unsignedTinyInteger('month'); // 1-12
                $table->unsignedInteger('forum_posts')->default(0); // Posts created
                $table->unsignedInteger('forum_comments')->default(0); // Comments made
                $table->timestamps();

                $table->unique(['user_id', 'year', 'month'], 'user_forum_engagement_unique');
                $table->index(['user_id', 'year', 'month']);
            });

            // Add foreign key constraint separately
            if (Schema::hasTable('users')) {
                try {
                    DB::statement('ALTER TABLE forum_engagements ADD CONSTRAINT forum_engagements_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails, continue without it
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_engagements');
    }
};

