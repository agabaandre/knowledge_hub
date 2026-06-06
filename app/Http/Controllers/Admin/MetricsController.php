<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Repositories\GraphsRepository;
use App\Repositories\MetricsRepository;
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
        $indicators = $this->graphsRepository->get_published_map_indicators();
        $defaultKpiId = (int) ($indicators->first()->id ?? 0);

        return [
            'map_indicators' => $indicators,
            'map_regions' => Region::query()->orderBy('region_name')->get(['id', 'region_name']),
            'initial_kpi_map' => $defaultKpiId > 0
                ? $this->graphsRepository->get_indicator_map_values($defaultKpiId, null)
                : null,
            'continental_indicators' => $this->graphsRepository->get_continental_indicator_summaries(),
            'map_data_url' => route('countries.map-data'),
        ];
    }

    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $country = $request->input('country');

        $useFilters = $from || $to || $country;
        $cacheKey = 'metrics_chart_data' . ($useFilters ? '_' . md5(serialize([$from, $to, $country])) : '');

        $minutes = 60 * 6;
        $chart_data = cache()->remember($cacheKey, $useFilters ? 1 : $minutes, function () use ($from, $to, $country) {
            $data['visits_over_time'] = $this->metricsRepository->visits_over_time($from, $to, $country);
            $data['visits_by_country'] = $this->metricsRepository->country_access($from, $to, $country);
            $data['signups_by_country'] = $this->metricsRepository->country_signups($from, $to, $country);
            $data['monthly_signups'] = $this->metricsRepository->monthly_signups($from, $to);
            $data['monthly_publications'] = $this->metricsRepository->monthly_publications($from, $to);
            return $data;
        });

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.metrics.graphs_html', array_merge([
                    'from' => $from,
                    'to' => $to,
                    'country' => $country,
                ], $this->africaMapContext()))->render(),
                'chart_data' => $chart_data,
                'visit_countries' => $this->metricsRepository->listVisitCountries($from, $to),
                'filters' => [
                    'from' => $from,
                    'to' => $to,
                    'country' => $country,
                ],
                'africa_map' => $this->africaMapContext(),
            ]);
        }

        return view('admin.metrics.index', ['chart_data' => $chart_data]);
    }
}
