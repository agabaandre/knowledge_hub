<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('community_of_practice_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_type_id')->constrained()->onDelete('cascade');
            $table->integer('year'); // Year the badge was awarded
            $table->integer('month'); // Month the badge was awarded (1-12)
            $table->integer('contributions_count'); // Number of contributions that earned this badge
            $table->timestamp('awarded_at'); // When the badge was awarded
            $table->boolean('email_sent')->default(false); // Track if notification email was sent
            $table->timestamps();
            
            // Prevent duplicate badges for same user/community/month/year/badge_type
            $table->unique(['user_id', 'community_of_practice_id', 'badge_type_id', 'year', 'month'], 'unique_user_badge');
            
            // Indexes for performance
            $table->index(['user_id', 'community_of_practice_id']);
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_badges');
    }
}
