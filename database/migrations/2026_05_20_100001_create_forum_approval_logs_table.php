<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id')->index();
            $table->string('action', 50);
            $table->unsignedBigInteger('performed_by')->nullable()->index();
            $table->string('performed_by_name')->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_approval_logs');
    }
};
