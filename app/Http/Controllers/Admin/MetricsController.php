<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Repositories\GraphsRepository;
use App\Repositories\MetricsRepository;
use App\Support\MetricsCache;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    private $metricsRepository;

    private $graphsRepository;

    public function __construct(MetricsRepository $metricsRepository, GraphsRepository $graphsRepository)
    {
        $this->metricsRepository = $metricsRepository;
        $this->graphsRepository = $graphsRepository;
    }

    private function africaMapContext(): array
    {
        $cache = MetricsCache::store();
        $ttl = MetricsCache::ttl('map_context');
        $indicators = $this->graphsRepository->get_published_map_indicators();

        $initialKpiMap = $cache->remember(
            'metrics_initial_publications_map',
            $ttl,
            fn () => $this->graphsRepository->get_member_state_map_values(0, null, 'admin_metrics')
        );

        $continentalIndicators = $cache->remember(
            'metrics_continental_indicators',
            $ttl,
            fn () => $this->graphsRepository->get_continental_indicator_summaries()
        );

        return [
            'map_indicators' => $indicators,
            'map_regions' => Region::query()->orderBy('region_name')->get(['id', 'region_name']),
            'initial_kpi_map' => $initialKpiMap,
            'continental_indicators' => $continentalIndicators,
            'map_data_url' => route('countries.map-data'),
        ];
    }

    private function chartData(?string $from, ?string $to, ?string $country): array
    {
        $useFilters = $from || $to || $country;
        $cacheKey = 'metrics_chart_data_v2'.($useFilters ? '_'.md5(serialize([$from, $to, $country])) : '_all');
        $ttl = MetricsCache::ttl($useFilters ? 'filtered' : 'default');

        return MetricsCache::store()->remember($cacheKey, $ttl, function () use ($from, $to, $country) {
            return [
                'visits_over_time' => $this->metricsRepository->visits_over_time($from, $to, $country),
                'visits_by_country' => $this->metricsRepository->country_access($from, $to, $country),
                'signups_over_time' => $this->metricsRepository->signups_over_time($from, $to, $country),
                'signups_by_country' => $this->metricsRepository->country_signups($from, $to, $country),
                'monthly_signups' => $this->metricsRepository->monthly_signups($from, $to, $country),
                'monthly_publications' => $this->metricsRepository->monthly_publications($from, $to),
            ];
        });
    }

    private function liveChartData(?string $from, ?string $to, ?string $country): array
    {
        $cacheKey = 'metrics_live_'.md5(serialize([$from, $to, $country]));
        $ttl = MetricsCache::ttl('live');

        return MetricsCache::store()->remember($cacheKey, $ttl, function () use ($from, $to, $country) {
            return [
                'visits_over_time' => $this->metricsRepository->visits_over_time($from, $to, $country),
                'signups_over_time' => $this->metricsRepository->signups_over_time($from, $to, $country),
            ];
        });
    }

    private function summaryTotals(array $chartData): array
    {
        $visits = $chartData['visits_over_time']['values'] ?? [];
        $signups = $chartData['signups_over_time']['values'] ?? [];

        return [
            'total_visits' => array_sum($visits),
            'total_signups' => array_sum($signups),
            'countries_with_visits' => count($chartData['visits_by_country']['values'] ?? []),
            'countries_with_signups' => count($chartData['signups_by_country']['values'] ?? []),
        ];
    }

    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $country = $request->input('country');

        $chart_data = $this->chartData($from, $to, $country);
        $mapContext = $this->africaMapContext();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.metrics.graphs_html', array_merge([
                    'from' => $from,
                    'to' => $to,
                    'country' => $country,
                ], $mapContext))->render(),
                'chart_data' => $chart_data,
                'summary' => $this->summaryTotals($chart_data),
                'visit_countries' => $this->metricsRepository->listVisitCountries($from, $to),
                'filters' => [
                    'from' => $from,
                    'to' => $to,
                    'country' => $country,
                ],
                'africa_map' => $mapContext,
                'cache' => [
                    'redis' => MetricsCache::redisAvailable(),
                ],
            ]);
        }

        return view('admin.metrics.index', array_merge([
            'chart_data' => $chart_data,
            'summary' => $this->summaryTotals($chart_data),
        ], $mapContext));
    }

    /**
     * Lightweight endpoint for live chart polling (short TTL, Redis when available).
     */
    public function live(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $country = $request->input('country');

        $chart_data = $this->liveChartData($from, $to, $country);

        return response()->json([
            'chart_data' => $chart_data,
            'summary' => [
                'total_visits' => array_sum($chart_data['visits_over_time']['values'] ?? []),
                'total_signups' => array_sum($chart_data['signups_over_time']['values'] ?? []),
            ],
            'refreshed_at' => now()->toIso8601String(),
            'cache' => [
                'redis' => MetricsCache::redisAvailable(),
            ],
        ]);
    }
}
