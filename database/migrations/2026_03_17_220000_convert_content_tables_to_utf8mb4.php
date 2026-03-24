<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Convert core rich-text/content tables to utf8mb4 for multilingual support
     * (Arabic, Amharic, etc.) at DB level.
     */
    public function up(): void
    {
        $tables = [
            'publication',
            'publication_summaries',
            'publication_comments',
            'forums',
            'forum_comments',
            'events',
            'data_records',
            'facts',
            'thematic_areas',
            'sub_thematic_areas',
            'tags',
            'authors',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    public function down(): void
    {
        // Intentionally no-op: previous charset/collation may differ by environment.
    }
};

