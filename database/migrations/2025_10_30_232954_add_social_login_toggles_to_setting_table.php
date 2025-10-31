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
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'enable_microsoft_login')) {
                $table->boolean('enable_microsoft_login')->default(true)->after('show_quiz');
            }
            if (!Schema::hasColumn('setting', 'enable_google_login')) {
                $table->boolean('enable_google_login')->default(true)->after('enable_microsoft_login');
            }
            if (!Schema::hasColumn('setting', 'enable_linkedin_login')) {
                $table->boolean('enable_linkedin_login')->default(true)->after('enable_google_login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'enable_microsoft_login')) {
                $table->dropColumn('enable_microsoft_login');
            }
            if (Schema::hasColumn('setting', 'enable_google_login')) {
                $table->dropColumn('enable_google_login');
            }
            if (Schema::hasColumn('setting', 'enable_linkedin_login')) {
                $table->dropColumn('enable_linkedin_login');
            }
        });
    }
};
