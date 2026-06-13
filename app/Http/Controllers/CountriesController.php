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
use App\Models\Region;

class CountriesController extends Controller
{
    public const COUNTRY_PUBLICATIONS_INFINITE_ROWS = 5;

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

        $data['map_indicators'] = $this->dashRepo->get_published_map_indicators();
        $data['continental_indicators'] = $this->dashRepo->get_continental_indicator_summaries();
        $data['initial_map_data'] = $this->dashRepo->get_member_state_map_values(0, null, 'frontend_countries');
        $data['regions_json'] = $data['regions']->map(fn ($r) => [
            'id' => (int) $r->id,
            'name' => $r->region_name,
        ])->values();

        return view('countries.index',$data);
    }

    public function mapData(Request $request)
    {
        $kpiId = (int) $request->input('kpi_id', 0);
        $regionId = $request->filled('region_id') ? (int) $request->input('region_id') : null;

        $mapData = $this->dashRepo->get_member_state_map_values($kpiId, $regionId, 'frontend_countries');

        return response()->json([
            'map' => $mapData,
            'regional_summaries' => $this->dashRepo->get_regional_indicator_summaries($kpiId),
            'indicator_summaries' => $this->dashRepo->get_indicator_summaries_for_scope($regionId),
            'scope' => $this->indicatorScopeMeta($regionId),
        ]);
    }

    public function indicatorSummaries(Request $request)
    {
        $regionId = $request->filled('region_id') ? (int) $request->input('region_id') : null;

        return response()->json([
            'summaries' => $this->dashRepo->get_indicator_summaries_for_scope($regionId),
            'scope' => $this->indicatorScopeMeta($regionId),
        ]);
    }

    private function indicatorScopeMeta(?int $regionId): array
    {
        $regionName = null;
        if ($regionId) {
            $regionName = Region::query()->whereKey($regionId)->value('region_name');
        }

        $scopeName = $regionName ?: 'Africa (all member states)';

        return [
            'region_id' => $regionId,
            'name' => $scopeName,
            'title' => $regionId ? 'Regional indicators' : 'Continental indicators',
            'subtitle' => $regionId
                ? 'Regional averages or totals for '.$scopeName.'. Click any card to explore it on the map.'
                : 'Africa-wide averages or totals from published indicators. Click any card to explore it on the map.',
        ];
    }


	public function country(Request $request, ?string $slug = null){

        if (! $slug && $request->filled('state')) {
            $legacyCountry = $this->areasRepo->member_state((int) $request->state);
            if (! $legacyCountry) {
                abort(404);
            }
            if (seo_friendly_urls_enabled() && ! empty($legacyCountry->slug)) {
                return redirect()->to(country_detail_url($legacyCountry), 301);
            }
        }

        $country = $this->resolveMemberState($request, $slug);
        $countryId = (int) $country->id;
        $data['country'] = $country;

        $request['area'] = $countryId;
        $request['state'] = $countryId;
        $this->prepareCountryPublicationsListingRequest($request);
		$data['publications']   = $this->publicationsRepo->getLightweight($request);
        $data['countryPublicationsInfiniteScroll'] = $this->countryPublicationsInfiniteScrollEnabled();
        $data['listingInfiniteScroll'] = $data['countryPublicationsInfiniteScroll'];
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
            $item->display = kpi_indicator_display(
                (float) ($item->kpi_value ?? 0),
                $item->unit_label ?? null,
                $item->kpi_name ?? null
            );
            $item->has_drilldown = $item->has_chart
                || ! empty($item->narration)
                || ! empty($item->owid_chart_url);

            if ($item->has_drilldown) {
                $data['kpi_chart_payload'][$kpiId] = [
                    'kpi_id' => $kpiId,
                    'name' => $item->kpi_name ?? '',
                    'unit' => $item->display['unit'] ?? '',
                    'unit_plain' => $item->display['unit_plain'] ?? '',
                    'unit_full' => $item->display['unit_full'] ?? '',
                    'display_value' => $item->display['value'] ?? '',
                    'value_with_unit' => $item->display['value_with_unit'] ?? '',
                    'chart_unit' => $item->display['chart_unit'] ?? 'Value',
                    'value_type' => $item->display['type'] ?? 'other',
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

    public function countryPublicationsPage(Request $request, ?string $slug = null)
    {
        if (! $this->countryPublicationsInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $country = $this->resolveMemberState($request, $slug);
        $countryId = (int) $country->id;

        $request->merge([
            'area' => $countryId,
            'state' => $countryId,
        ]);
        $this->prepareCountryPublicationsListingRequest($request);
        $request->merge(['rows' => self::COUNTRY_PUBLICATIONS_INFINITE_ROWS]);

        $publications = $this->publicationsRepo->getLightweight($request);

        $page = (int) $publications->currentPage();
        $perPage = (int) $publications->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($publications->total(), $listOffset + $publications->count());

        return response()->json([
            'ok' => true,
            'html' => view('publications.partials.publications_list_items', [
                'publications' => $publications,
                'listOffset' => $listOffset,
            ])->render(),
            'current_page' => $page,
            'last_page' => (int) $publications->lastPage(),
            'has_more' => $publications->hasMorePages(),
            'total' => (int) $publications->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function resolveMemberState(Request $request, ?string $slug = null)
    {
        if ($slug) {
            $country = $this->areasRepo->member_state_by_slug($slug);
            if (! $country) {
                abort(404);
            }

            return $country;
        }

        if (! $request->filled('state')) {
            abort(404);
        }

        $country = $this->areasRepo->member_state((int) $request->state);
        if (! $country) {
            abort(404);
        }

        return $country;
    }

    protected function prepareCountryPublicationsListingRequest(Request $request): void
    {
        if ($this->countryPublicationsInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
                'rows' => self::COUNTRY_PUBLICATIONS_INFINITE_ROWS,
            ]);
        }
    }

    protected function countryPublicationsInfiniteScrollEnabled(): bool
    {
        return (settings()->country_publications_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }



}
