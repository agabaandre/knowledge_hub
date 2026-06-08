<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $updates = [];
        foreach (['enable_microsoft_login', 'enable_google_login', 'enable_linkedin_login'] as $column) {
            if (Schema::hasColumn('setting', $column)) {
                $updates[$column] = true;
            }
        }

        if ($updates !== []) {
            DB::table('setting')->update($updates);
        }
    }

    public function down(): void
    {
        // No-op: prior per-row values are not restored automatically.
    }
};
