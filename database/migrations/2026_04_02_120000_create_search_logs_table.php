<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('term', 255);
            $table->unsignedInteger('results_count')->nullable();
            $table->string('request_path', 191)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
