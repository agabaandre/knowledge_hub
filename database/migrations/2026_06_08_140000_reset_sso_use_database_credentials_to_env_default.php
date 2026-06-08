<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            return;
        }

        DB::table('setting')->update(['sso_use_database_credentials' => false]);
    }

    public function down(): void
    {
        // No-op: previous per-row values are not recoverable.
    }
};
