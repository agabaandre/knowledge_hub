<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tags') || ! Schema::hasColumn('tags', 'tag_text')) {
            return;
        }

        DB::statement('ALTER TABLE tags MODIFY tag_text VARCHAR(255) NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('tags') || ! Schema::hasColumn('tags', 'tag_text')) {
            return;
        }

        DB::statement('ALTER TABLE tags MODIFY tag_text VARCHAR(20) NOT NULL');
    }
};
