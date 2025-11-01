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
        Schema::create('community_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_of_practice_id');
            $table->string('email');
            $table->string('token')->unique();
            $table->unsignedBigInteger('invited_by');
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('community_of_practice_id')->references('id')->on('community_of_practices')->onDelete('cascade');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['email', 'community_of_practice_id']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_invitations');
    }
};
