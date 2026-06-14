<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_of_practice_id');
            $table->text('comment');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('status')->default('approved');
            $table->timestamps();
        });

        Schema::create('community_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_comment_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['community_comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_comment_likes');
        Schema::dropIfExists('community_comments');
    }
};
