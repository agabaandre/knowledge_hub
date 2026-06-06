<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('data')) {
            DB::table('data')->truncate();
        }
        if (Schema::hasTable('kpi')) {
            DB::table('kpi')->truncate();
        }

        if (Schema::hasTable('subject_areas')) {
            DB::table('subject_areas')->truncate();
        }

        if (Schema::hasTable('subject_areas')) {
            Schema::table('subject_areas', function (Blueprint $table) {
                if (! Schema::hasColumn('subject_areas', 'slug')) {
                    $table->string('slug', 191)->nullable()->after('name');
                }
                if (! Schema::hasColumn('subject_areas', 'owid_topic')) {
                    $table->string('owid_topic', 191)->nullable()->after('slug');
                }
                if (! Schema::hasColumn('subject_areas', 'owid_search_query')) {
                    $table->string('owid_search_query', 255)->nullable()->after('owid_topic');
                }
                if (! Schema::hasColumn('subject_areas', 'description')) {
                    $table->text('description')->nullable()->after('owid_search_query');
                }
                if (! Schema::hasColumn('subject_areas', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(100)->after('description');
                }
                if (! Schema::hasColumn('subject_areas', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                }
            });
        }

        if (Schema::hasTable('kpi')) {
            Schema::table('kpi', function (Blueprint $table) {
                if (! Schema::hasColumn('kpi', 'owid_chart_slug')) {
                    $table->string('owid_chart_slug', 191)->nullable()->unique('kpi_owid_chart_slug_unique')->after('frequency');
                }
                if (! Schema::hasColumn('kpi', 'owid_url')) {
                    $table->string('owid_url', 500)->nullable()->after('owid_chart_slug');
                }
                if (! Schema::hasColumn('kpi', 'owid_variant_name')) {
                    $table->string('owid_variant_name', 255)->nullable()->after('owid_url');
                }
                if (! Schema::hasColumn('kpi', 'unit_label')) {
                    $table->string('unit_label', 120)->nullable()->after('owid_variant_name');
                }
                if (! Schema::hasColumn('kpi', 'source')) {
                    $table->string('source', 50)->default('manual')->after('unit_label');
                }
                if (! Schema::hasColumn('kpi', 'status')) {
                    $table->string('status', 20)->default('draft')->after('source');
                }
                if (! Schema::hasColumn('kpi', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                }
                if (! Schema::hasColumn('kpi', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (! Schema::hasColumn('kpi', 'recalled_at')) {
                    $table->timestamp('recalled_at')->nullable()->after('approved_at');
                }
                if (! Schema::hasColumn('kpi', 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable()->after('recalled_at');
                }
                if (! Schema::hasColumn('kpi', 'metadata')) {
                    $table->json('metadata')->nullable()->after('last_synced_at');
                }
            });
        }

        if (Schema::hasTable('data') && Schema::hasColumn('data', 'value')) {
            DB::statement('ALTER TABLE `data` MODIFY `value` DECIMAL(20,4) NOT NULL');
            if (Schema::hasColumn('data', 'data_source')) {
                DB::statement('ALTER TABLE `data` MODIFY `data_source` INT NULL');
            }
        }

        if (! Schema::hasTable('kpi_narrations')) {
            Schema::create('kpi_narrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('kpi_id');
                $table->unsignedInteger('country_id');
                $table->string('period', 11);
                $table->text('narration');
                $table->string('ai_model', 80)->nullable();
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();
                $table->unique(['kpi_id', 'country_id', 'period'], 'kpi_narrations_unique');
                $table->index(['country_id', 'kpi_id']);
            });
        }

        $this->seedSubjectAreas();
        $this->refreshKpiDataView();
    }

    protected function seedSubjectAreas(): void
    {
        if (! Schema::hasTable('subject_areas')) {
            return;
        }

        $id = 1;
        foreach (config('owid.default_subject_areas', []) as $row) {
            $record = [
                'id' => $id,
                'name' => $row['name'],
                'slug' => \Illuminate\Support\Str::slug($row['name']),
                'owid_topic' => $row['owid_topic'] ?? null,
                'description' => 'Indicators sourced from Our World in Data — '.$row['name'],
                'sort_order' => $row['sort_order'] ?? ($id * 10),
                'is_active' => true,
                'created' => now(),
                'updated' => now(),
            ];

            if (Schema::hasColumn('subject_areas', 'owid_search_query')) {
                $record['owid_search_query'] = $row['owid_search_query'] ?? null;
            }

            DB::table('subject_areas')->insert($record);
            $id++;
        }
    }

    protected function refreshKpiDataView(): void
    {
        if (! Schema::hasTable('kpi_data_view') && ! Schema::hasTable('data')) {
            return;
        }

        DB::statement('DROP VIEW IF EXISTS `kpi_data_view`');
        DB::statement(<<<'SQL'
CREATE VIEW `kpi_data_view` AS
SELECT
    k.id AS kpi_id,
    SUM(d.value) AS kpi_value,
    d.period,
    YEAR(CONCAT(d.period, '-01')) AS period_year,
    MONTH(CONCAT(d.period, '-01')) AS period_month,
    k.name AS kpi_name,
    k.subject_area AS subject_area_id,
    k.description,
    k.frequency,
    k.status AS kpi_status,
    k.unit_label,
    k.owid_chart_slug,
    k.owid_url,
    c.name AS country_name,
    c.color AS country_color,
    c.latitude,
    c.longitude,
    c.id AS country_id,
    c.iso3_code AS country_iso3
FROM data d
JOIN kpi k ON d.kpi_id = k.id
JOIN country c ON d.country_id = c.id
GROUP BY d.kpi_id, d.period, d.country_id
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_narrations');

        if (Schema::hasTable('kpi')) {
            Schema::table('kpi', function (Blueprint $table) {
                foreach (['metadata', 'last_synced_at', 'recalled_at', 'approved_at', 'approved_by', 'status', 'source', 'unit_label', 'owid_variant_name', 'owid_url', 'owid_chart_slug'] as $col) {
                    if (Schema::hasColumn('kpi', $col)) {
                        if ($col === 'owid_chart_slug') {
                            $table->dropUnique('kpi_owid_chart_slug_unique');
                        }
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('subject_areas')) {
            Schema::table('subject_areas', function (Blueprint $table) {
                foreach (['is_active', 'sort_order', 'description', 'owid_search_query', 'owid_topic', 'slug'] as $col) {
                    if (Schema::hasColumn('subject_areas', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
