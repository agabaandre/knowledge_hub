<?php

namespace App\Http\Controllers;

use App\Repositories\AreasRepository;
use App\Repositories\AuthorsRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\GraphsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ThemesRepository;
use Illuminate\Http\Request;
use App\Models\KpiNarration;

class CountriesController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo,$areasRepo,$forumsRepo,$themesRepo,$dashRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,
	AreasRepository $areasRepo,ForumsRepository $forumsRepo,ThemesRepository $themesRepo,GraphsRepository $dashRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
		$this->areasRepo        = $areasRepo;
		$this->forumsRepo 		= $forumsRepo;
		$this->themesRepo       = $themesRepo;
        $this->dashRepo         = $dashRepo;
        
    }
    
    public function index(Request $request){

        $data['countries'] = $this->areasRepo->member_states($request);
        $data['regions']  = $this->areasRepo->regions();
        
        // Pre-calculate resources per region (unique publications + forums)
        $regionResources = [];
        foreach ($data['regions'] as $region) {
            $regionResources[$region->id] = [
                'publications' => $this->areasRepo->getUniqueResourcesByRegion($region->id),
                'forums' => $this->areasRepo->getForumsByRegion($region->id),
                'total' => $this->areasRepo->getTotalResourcesByRegion($region->id)
            ];
        }
        $data['region_resources'] = $regionResources;

        return view('countries.index',$data);
    }


	public function country(Request $request, ?string $slug = null){

        if ($slug) {
            $data['country'] = $this->areasRepo->member_state_by_slug($slug);
            if (! $data['country']) {
                abort(404);
            }
            $countryId = (int) $data['country']->id;
        } else {
            if (! $request->filled('state')) {
                abort(404);
            }

            $countryId = (int) $request->state;
            $data['country'] = $this->areasRepo->member_state($countryId);
            if (! $data['country']) {
                abort(404);
            }

            if (seo_friendly_urls_enabled() && ! empty($data['country']->slug)) {
                return redirect()->to(country_detail_url($data['country']), 301);
            }
        }

        $request['area'] = $countryId;
        $request['state'] = $countryId;
		$data['publications']   = $this->publicationsRepo->getLightweight($request);
        $kpiRows = $this->dashRepo->get_country_kpis(['country_id' => $countryId], false, true);
        $kpiIds = collect($kpiRows)->pluck('kpi_id')->unique()->filter()->map(fn ($id) => (int) $id)->all();
        $seriesMap = $this->dashRepo->get_country_kpi_time_series($countryId, $kpiIds, true);

        $narrationMap = KpiNarration::query()
            ->where('country_id', $countryId)
            ->whereIn('kpi_id', $kpiIds)
            ->get()
            ->keyBy(fn ($n) => $n->kpi_id.'|'.$n->period);

        $data['kpi_chart_payload'] = [];
        $data['kpis'] = [];

        foreach ($kpiRows as $row) {
            $item = is_array($row) ? (object) $row : $row;
            $kpiId = (int) ($item->kpi_id ?? 0);
            $key = $kpiId.'|'.($item->period ?? '');
            $series = $seriesMap[$kpiId] ?? ['labels' => [], 'values' => []];
            $item->series = $series;
            $item->period_count = count($series['values']);
            $item->has_chart = $item->period_count > 1;
            $item->narration = $narrationMap[$key]->narration ?? null;
            $item->owid_chart_url = owid_chart_url($item);
            $item->has_drilldown = $item->has_chart
                || ! empty($item->narration)
                || ! empty($item->owid_chart_url);

            if ($item->has_drilldown) {
                $data['kpi_chart_payload'][$kpiId] = [
                    'kpi_id' => $kpiId,
                    'name' => $item->kpi_name ?? '',
                    'unit' => $item->unit_label ?? '',
                    'latest_value' => (float) ($item->kpi_value ?? 0),
                    'latest_period' => substr((string) ($item->period ?? ''), 0, 4),
                    'labels' => $series['labels'],
                    'values' => $series['values'],
                    'narration' => $item->narration,
                    'owid_chart_url' => $item->owid_chart_url,
                    'has_chart' => $item->has_chart,
                ];
            }

            $data['kpis'][] = $item;
        }

        $data['kpi_groups'] = $this->dashRepo->group_country_kpis_by_subject($data['kpis']);

        $data['engagement_stats'] = $countryId > 0
            ? $this->areasRepo->memberStateEngagementStats($countryId)
            : [
                'publications_count' => 0,
                'forum_discussions_count' => 0,
                'enrolled_users_count' => 0,
            ];

        return view('countries.details',$data);
    }



}
