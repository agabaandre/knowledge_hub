<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\Region;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreasRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas = GeoCoverage::orderBy('id','desc');

        if($request->term)
        $areas->where('name','like','%'.$request->term.'%');

        $result = $areas->paginate($rows_count);

        return $result;
    }


    public function find($id){

        return Author::find($id);
    }

    // public function member_states(){
    //     return Country::where('region_id','>',0)
    //     ->withCount('publications as resources')
    //     ->orderBy('name','asc')->get();
    // }

    public function member_states() {
        return Country::where('region_id', '>', 0)
            ->withCount('publications as resources') 
            ->orderBy('name', 'asc')
            ->get();
    }

    public function member_state($id){
        return Country::where('id',$id)->first();
    }

    public function member_state_by_slug(string $slug){
        return Country::where('slug', $slug)->first();
    }

    public function regions()
    {
        return Region::all();
    }

    public function countries(){

        return Country::all();
    }


    public function count(){
        return count(GeoCoverage::all());
    }

    public function save(Request $request) {
        
        $id = $request->input('area_id');
        $name = $request->input('area_name');

        $area = GeoCoverage::findOrNew($id);
        $area->name      = $name;
        $area->iso_code  = $request->iso_code;
        $area->iso3_code = $request->iso3_code;
        $area->svg_path  = $request->map;
        $area->base_url  = $request->base_url;
        $area->is_app_supported = ($request->app_supported)?$request->app_supported:false;

        if($request->hasFile('flag')):
            //upload photo
            $file        = $request->file( 'flag');
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
            $file->move(public_path('assets/img/flags/'),$file_path);
            $area->flag  = $file_path;
        endif;

        $area->save();

        return $area ?? null;
    }

    public function delete($id){
        return GeoCoverage::find($id)->delete();
    }

    public function member_states_count($value) {
        return Country::where('region_id', '>', 0)
            ->where('geographical_coverage_id', $value)
            ->orWhereHas('countries', function($subQuery) use ($value) {
                $subQuery->where('country.id', $value);
            })
            ->count();
    }

    /**
     * Get unique resources (publications) count for a region
     * Uses distinct publication IDs to avoid counting duplicates
     */
    public function getUniqueResourcesByRegion($regionId) {
        // Get all country IDs in this region
        $countryIds = Country::where('region_id', $regionId)->pluck('id');
        
        if ($countryIds->isEmpty()) {
            return 0;
        }
        
        // Count distinct publications linked to countries in this region
        // Use selectRaw with COUNT(DISTINCT) for accurate unique count
        $result = DB::table('publication_countries')
            ->whereIn('country_id', $countryIds)
            ->selectRaw('COUNT(DISTINCT publication_id) as count')
            ->value('count') ?? 0;
        
        return (int) $result;
    }

    /**
     * Get forums count for a region based on publisher's country
     */
    public function getForumsByRegion($regionId) {
        // Get all country IDs in this region
        $countryIds = Country::where('region_id', $regionId)->pluck('id');
        
        if ($countryIds->isEmpty()) {
            return 0;
        }
        
        // Count forums where the publisher's (user's) country is in this region
        // Check if status column exists, otherwise just count all forums
        $forums = Forum::whereHas('user', function($query) use ($countryIds) {
                $query->whereIn('country_id', $countryIds);
            });
        
        // Only filter by status if the column exists
        if (DB::getSchemaBuilder()->hasColumn('forums', 'status')) {
            $forums->where('status', 1); // Only count active/approved forums
        }
        
        return $forums->count();
    }

    /**
     * Get total resources (unique publications + forums) for a region
     */
    public function getTotalResourcesByRegion($regionId) {
        $uniquePublications = $this->getUniqueResourcesByRegion($regionId);
        $forums = $this->getForumsByRegion($regionId);
        return $uniquePublications + $forums;
    }

    /**
     * Regions with country counts (for API lookup / docs). Exposed as `countries_count` on each region model.
     */
    public function regionsWithCountryCounts()
    {
        return Region::query()
            ->withCount('countries')
            ->orderBy('region_name')
            ->get();
    }

    /**
     * Engagement stats for one member state (matches filters used on the public country details page for publications).
     */
    public function memberStateEngagementStats(int $countryId): array
    {
        $publicationsCount = (int) DB::table('publication_countries as pc')
            ->join('publication as p', 'p.id', '=', 'pc.publication_id')
            ->where('pc.country_id', $countryId)
            ->where('p.is_version', 0)
            ->where('p.is_admin_only_access', 0)
            ->where('p.is_active', 'Active')
            ->where('p.is_approved', 1)
            ->distinct()
            ->count('p.id');

        $forumThreadsCount = (int) Forum::query()
            ->where('status', 1)
            ->where('is_approved', 1)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            })
            ->whereHas('user', fn ($q) => $q->where('country_id', $countryId))
            ->count();

        $enrolledUsersCount = (int) User::query()->where('country_id', $countryId)->count();

        return [
            'publications_count' => $publicationsCount,
            'forum_discussions_count' => $forumThreadsCount,
            'enrolled_users_count' => $enrolledUsersCount,
        ];
    }

    /**
     * Member states (country.region_id set) with publication, forum, and user enrolment aggregates for API consumers.
     *
     * @return list<array<string, mixed>>
     */
    public function memberStatesWithStats(): array
    {
        $countries = Country::query()
            ->where('region_id', '>', 0)
            ->with('region')
            ->orderBy('name')
            ->get();

        $publicationCounts = DB::table('publication_countries as pc')
            ->join('publication as p', 'p.id', '=', 'pc.publication_id')
            ->where('p.is_version', 0)
            ->where('p.is_admin_only_access', 0)
            ->where('p.is_active', 'Active')
            ->where('p.is_approved', 1)
            ->groupBy('pc.country_id')
            ->select('pc.country_id', DB::raw('COUNT(DISTINCT pc.publication_id) as c'))
            ->pluck('c', 'country_id')
            ->mapWithKeys(fn ($v, $k) => [(int) $k => (int) $v]);

        $forumCounts = DB::table('forums as f')
            ->join('users as u', 'u.id', '=', 'f.created_by')
            ->where('f.status', 1)
            ->where('f.is_approved', 1)
            ->where(function ($q) {
                $q->where('f.is_rejected', 0)->orWhereNull('f.is_rejected');
            })
            ->groupBy('u.country_id')
            ->select('u.country_id', DB::raw('COUNT(*) as c'))
            ->pluck('c', 'country_id')
            ->mapWithKeys(fn ($v, $k) => [(int) $k => (int) $v]);

        $userCounts = User::query()
            ->whereNotNull('country_id')
            ->groupBy('country_id')
            ->selectRaw('country_id, COUNT(*) as c')
            ->pluck('c', 'country_id')
            ->mapWithKeys(fn ($v, $k) => [(int) $k => (int) $v]);

        return $countries->map(function (Country $country) use ($publicationCounts, $forumCounts, $userCounts) {
            $cid = (int) $country->id;

            return [
                'id' => $cid,
                'name' => $country->name,
                'region_id' => $country->region_id ? (int) $country->region_id : null,
                'region' => $country->relationLoaded('region') && $country->region
                    ? [
                        'id' => (int) $country->region->id,
                        'region_name' => $country->region->region_name,
                    ]
                    : null,
                'stats' => [
                    'publications_count' => (int) ($publicationCounts->get($cid, 0)),
                    'forum_discussions_count' => (int) ($forumCounts->get($cid, 0)),
                    'enrolled_users_count' => (int) ($userCounts->get($cid, 0)),
                ],
            ];
        })->values()->all();
    }

}
