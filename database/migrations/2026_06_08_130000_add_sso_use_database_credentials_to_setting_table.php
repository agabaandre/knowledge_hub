<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'sso_use_database_credentials')) {
                $table->boolean('sso_use_database_credentials')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            $table->dropColumn('sso_use_database_credentials');
        });
    }
};
