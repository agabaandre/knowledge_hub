<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'theme_preference')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('theme_preference', 20)->default('light')->after('remember_token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'theme_preference')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('theme_preference');
            });
        }
    }
};
