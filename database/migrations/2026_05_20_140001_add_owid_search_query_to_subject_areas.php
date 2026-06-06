<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subject_areas')) {
            return;
        }

        Schema::table('subject_areas', function (Blueprint $table) {
            if (! Schema::hasColumn('subject_areas', 'owid_search_query')) {
                $table->string('owid_search_query', 255)->nullable()->after('owid_topic');
            }
        });

        foreach (config('owid.default_subject_areas', []) as $row) {
            DB::table('subject_areas')
                ->where('name', $row['name'])
                ->update([
                    'owid_topic' => $row['owid_topic'] ?? null,
                    'owid_search_query' => $row['owid_search_query'] ?? null,
                    'sort_order' => $row['sort_order'] ?? 100,
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('subject_areas')) {
            return;
        }

        Schema::table('subject_areas', function (Blueprint $table) {
            if (Schema::hasColumn('subject_areas', 'owid_search_query')) {
                $table->dropColumn('owid_search_query');
            }
        });
    }
};
