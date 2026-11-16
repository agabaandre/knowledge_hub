<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('provider', 32);
                $table->string('provider_user_id', 191);
                $table->string('email', 254)->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();
                $table->unique(['provider', 'provider_user_id']);
                $table->unique(['user_id', 'provider']);
            });
        }

        if (! Schema::hasTable('banned_emails')) {
            Schema::create('banned_emails', function (Blueprint $table) {
                $table->id();
                $table->string('email', 254)->unique();
                $table->string('reason', 500)->nullable();
                $table->unsignedBigInteger('banned_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('banned_emails');
    }
};
