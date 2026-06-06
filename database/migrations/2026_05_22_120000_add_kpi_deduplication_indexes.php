<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subject_areas') && Schema::hasColumn('subject_areas', 'slug')) {
            $this->dedupeSubjectAreaSlugs();

            Schema::table('subject_areas', function (Blueprint $table) {
                if (! $this->indexExists('subject_areas', 'subject_areas_slug_unique')) {
                    $table->unique('slug', 'subject_areas_slug_unique');
                }
            });
        }

        if (Schema::hasTable('data')) {
            $this->dedupeDataRows();

            Schema::table('data', function (Blueprint $table) {
                if (! $this->indexExists('data', 'data_kpi_country_period_unique')) {
                    $table->unique(['kpi_id', 'country_id', 'period'], 'data_kpi_country_period_unique');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subject_areas')) {
            Schema::table('subject_areas', function (Blueprint $table) {
                if ($this->indexExists('subject_areas', 'subject_areas_slug_unique')) {
                    $table->dropUnique('subject_areas_slug_unique');
                }
            });
        }

        if (Schema::hasTable('data')) {
            Schema::table('data', function (Blueprint $table) {
                if ($this->indexExists('data', 'data_kpi_country_period_unique')) {
                    $table->dropUnique('data_kpi_country_period_unique');
                }
            });
        }
    }

    protected function dedupeSubjectAreaSlugs(): void
    {
        $rows = DB::table('subject_areas')->orderBy('id')->get(['id', 'name', 'slug']);
        $seen = [];

        foreach ($rows as $row) {
            $slug = Str::slug((string) ($row->slug ?: $row->name)) ?: 'subject-area-'.$row->id;
            $base = $slug;
            $suffix = 2;

            while (isset($seen[strtolower($slug)])) {
                $slug = $base.'-'.$suffix;
                $suffix++;
            }

            $seen[strtolower($slug)] = true;

            if ($slug !== $row->slug) {
                DB::table('subject_areas')->where('id', $row->id)->update(['slug' => $slug]);
            }
        }
    }

    protected function dedupeDataRows(): void
    {
        $dupes = DB::table('data')
            ->select('kpi_id', 'country_id', 'period', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('kpi_id', 'country_id', 'period')
            ->having('total', '>', 1)
            ->get();

        foreach ($dupes as $dupe) {
            DB::table('data')
                ->where('kpi_id', $dupe->kpi_id)
                ->where('country_id', $dupe->country_id)
                ->where('period', $dupe->period)
                ->where('id', '!=', $dupe->keep_id)
                ->delete();
        }
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        $database = Schema::getConnection()->getDatabaseName();
        $result = DB::select(
            'SELECT COUNT(*) AS total FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return ((int) ($result[0]->total ?? 0)) > 0;
    }
};
