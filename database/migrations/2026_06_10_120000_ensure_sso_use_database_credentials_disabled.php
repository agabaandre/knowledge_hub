<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        if (! Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('sso_use_database_credentials')->default(false);
            });
        }

        DB::table('setting')->update(['sso_use_database_credentials' => false]);
    }

    public function down(): void
    {
        // No-op: prior per-row values are not recoverable.
    }
};
