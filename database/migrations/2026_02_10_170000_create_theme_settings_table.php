<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('theme', 32)->index(); // 'default' | 'theme1'
            $table->string('key', 128)->index();
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['theme', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
