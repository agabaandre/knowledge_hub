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
        $data['kpis'] = $this->dashRepo->get_country_kpis(['country_id' => $countryId]);
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
