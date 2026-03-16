<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MetricsRepository;

class MetricsController extends Controller
{
    private $metricsRepository;

    public function __construct(MetricsRepository $metricsRepository)
    {
        $this->metricsRepository = $metricsRepository;
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
            $data['visits_by_country'] = $this->metricsRepository->country_access($from, $to, $country);
            $data['signups_by_country'] = $this->metricsRepository->country_signups($from, $to, $country);
            $data['monthly_signups'] = $this->metricsRepository->monthly_signups($from, $to);
            $data['monthly_publications'] = $this->metricsRepository->monthly_publications($from, $to);
            return $data;
        });

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.metrics.graphs_html')->render(),
                'chart_data' => $chart_data,
            ]);
        }

        return view('admin.metrics.index', ['chart_data' => $chart_data]);
    }
}
