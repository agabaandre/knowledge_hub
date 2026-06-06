<?php

namespace App\Services\Owid;

use App\Models\Country;
use App\Models\Kpi;
use App\Models\KpiDataRecord;
use App\Models\SubjectArea;
use App\Services\Kpi\KpiDeduplicationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OwidIndicatorSyncService
{
    public function __construct(
        private OwidApiClient $client,
        private KpiDeduplicationService $dedupe
    ) {
    }

    /**
     * @return array{discovered: int, skipped: int, duplicates: int, subject_areas: int, errors: array<int, string>}
     */
    public function discoverIndicators(?int $subjectAreaId = null, ?callable $onProgress = null): array
    {
        $areas = SubjectArea::query()
            ->when($subjectAreaId, fn ($q) => $q->where('id', $subjectAreaId))
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNotNull('owid_topic')->where('owid_topic', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->whereNotNull('owid_search_query')->where('owid_search_query', '!=', '');
                });
            })
            ->orderBy('sort_order')
            ->get();

        $discovered = 0;
        $skipped = 0;
        $duplicates = 0;
        $errors = [];
        $areaTotal = max(1, $areas->count());

        foreach ($areas as $index => $area) {
            if ($onProgress) {
                $onProgress($index + 1, $areaTotal, 'Searching: '.$area->name);
            }

            try {
                $payload = $this->client->searchChartsForSubject(
                    topic: $area->owid_topic ?: null,
                    searchQuery: $area->owid_search_query ?: null,
                    hitsPerPage: (int) config('owid.charts_per_topic', 12)
                );
            } catch (\Throwable $e) {
                Log::warning('owid.discover_failed', [
                    'subject_area_id' => $area->id,
                    'name' => $area->name,
                    'message' => $e->getMessage(),
                ]);
                $errors[] = $area->name.': '.$e->getMessage();
                continue;
            }

            foreach ($payload['results'] ?? [] as $chart) {
                $slug = (string) ($chart['slug'] ?? '');
                if ($slug === '') {
                    $skipped++;
                    continue;
                }

                $chartType = (string) ($chart['type'] ?? 'chart');
                if ($chartType !== 'chart') {
                    $skipped++;
                    continue;
                }

                if (Kpi::query()->whereRaw('LOWER(owid_chart_slug) = ?', [strtolower($slug)])->exists()) {
                    $skipped++;
                    $duplicates++;
                    continue;
                }

                $existing = $this->dedupe->findExistingIndicatorForChart($chart);
                if ($existing) {
                    if ((int) $existing->subject_area !== (int) $area->id && empty($existing->subject_area)) {
                        $existing->subject_area = $area->id;
                        $existing->save();
                    }
                    $skipped++;
                    $duplicates++;
                    continue;
                }

                Kpi::query()->create([
                    'name' => (string) ($chart['title'] ?? $slug),
                    'description' => (string) ($chart['subtitle'] ?? ''),
                    'subject_area' => $area->id,
                    'computation_method' => 'Latest value from OWID chart',
                    'frequency' => 'Annual',
                    'owid_chart_slug' => $slug,
                    'owid_url' => (string) ($chart['url'] ?? config('owid.attribution_url').'/grapher/'.$slug),
                    'owid_variant_name' => (string) ($chart['variantName'] ?? ''),
                    'unit_label' => null,
                    'source' => 'owid',
                    'status' => 'draft',
                    'metadata' => $chart,
                ]);

                $discovered++;
            }
        }

        return [
            'discovered' => $discovered,
            'skipped' => $skipped,
            'duplicates' => $duplicates,
            'subject_areas' => $areas->count(),
            'errors' => $errors,
        ];
    }

    /**
     * @return array{indicators: int, rows: int, errors: array<int, string>}
     */
    public function syncIndicatorData(?int $kpiId = null, bool $publishedOnly = false, ?callable $onProgress = null): array
    {
        $countries = $this->memberStatesByIso3();
        $iso3List = array_keys($countries);

        $query = Kpi::query()->where('source', 'owid')->whereNotNull('owid_chart_slug');
        if ($publishedOnly) {
            $query->where('status', 'published');
        }
        if ($kpiId) {
            $query->where('id', $kpiId);
        }

        $indicators = $query->get();
        $rows = 0;
        $errors = [];
        $indicatorTotal = max(1, $indicators->count());

        foreach ($indicators as $index => $indicator) {
            if ($onProgress) {
                $onProgress($index + 1, $indicatorTotal, 'Syncing: '.$indicator->name);
            }

            try {
                $csv = $this->client->fetchChartCsv((string) $indicator->owid_chart_slug);
                $values = $this->client->parseLatestValuesByIso3($csv, $iso3List);

                if ($values !== []) {
                    $first = reset($values);
                    if (! empty($first['value_column']) && empty($indicator->unit_label)) {
                        $indicator->unit_label = (string) $first['value_column'];
                    }
                }

                DB::transaction(function () use ($indicator, $values, $countries, &$rows) {
                    KpiDataRecord::query()->where('kpi_id', $indicator->id)->delete();

                    foreach ($values as $iso3 => $row) {
                        $country = $countries[$iso3] ?? null;
                        if (! $country) {
                            continue;
                        }

                        KpiDataRecord::query()->create([
                            'kpi_id' => $indicator->id,
                            'country_id' => $country->id,
                            'value' => $row['value'],
                            'period' => $row['period'],
                            'data_source' => null,
                        ]);
                        $rows++;
                    }

                    $indicator->last_synced_at = now();
                    $indicator->save();
                });
            } catch (\Throwable $e) {
                Log::warning('owid.sync_failed', [
                    'kpi_id' => $indicator->id,
                    'slug' => $indicator->owid_chart_slug,
                    'message' => $e->getMessage(),
                ]);
                $errors[] = $indicator->name.': '.$e->getMessage();
            }
        }

        return [
            'indicators' => $indicators->count(),
            'rows' => $rows,
            'errors' => $errors,
        ];
    }

    public function approve(Kpi $kpi, ?int $userId = null): Kpi
    {
        $kpi->status = 'published';
        $kpi->approved_by = $userId;
        $kpi->approved_at = now();
        $kpi->recalled_at = null;
        $kpi->save();

        $this->syncIndicatorData($kpi->id);

        return $kpi->fresh();
    }

    public function recall(Kpi $kpi): Kpi
    {
        $kpi->status = 'recalled';
        $kpi->recalled_at = now();
        $kpi->save();

        return $kpi;
    }

    /**
     * @return array{approved: int, already_published: int, missing: int, errors: array<int, string>, kpi_ids: array<int, int>}
     */
    public function approveDefaultIndicators(?int $userId = null, ?callable $onProgress = null): array
    {
        $slugs = array_values(array_unique(array_filter(
            config('owid.default_published_chart_slugs', []),
            fn ($slug) => is_string($slug) && trim($slug) !== ''
        )));

        $approved = 0;
        $alreadyPublished = 0;
        $missing = 0;
        $errors = [];
        $kpiIds = [];
        $slugTotal = max(1, count($slugs));

        foreach ($slugs as $index => $slug) {
            if ($onProgress) {
                $onProgress($index + 1, $slugTotal, 'Publishing: '.$slug);
            }

            $kpi = Kpi::query()->where('owid_chart_slug', $slug)->first();
            if (! $kpi) {
                $missing++;
                $errors[] = 'Chart not discovered yet: '.$slug;
                continue;
            }

            if ($kpi->status === 'published') {
                $alreadyPublished++;
                $kpiIds[] = (int) $kpi->id;
                continue;
            }

            try {
                $kpi = $this->approve($kpi, $userId);
                $approved++;
                $kpiIds[] = (int) $kpi->id;
            } catch (\Throwable $e) {
                Log::warning('owid.approve_default_failed', [
                    'slug' => $slug,
                    'kpi_id' => $kpi->id,
                    'message' => $e->getMessage(),
                ]);
                $errors[] = $kpi->name.': '.$e->getMessage();
            }
        }

        return [
            'approved' => $approved,
            'already_published' => $alreadyPublished,
            'missing' => $missing,
            'errors' => $errors,
            'kpi_ids' => $kpiIds,
        ];
    }

    /**
     * @return array<string, Country>
     */
    protected function memberStatesByIso3(): array
    {
        return Country::query()
            ->where('region_id', '>', 0)
            ->whereNotNull('iso3_code')
            ->where('iso3_code', '!=', '')
            ->get()
            ->keyBy(fn (Country $c) => strtoupper((string) $c->iso3_code))
            ->all();
    }
}
