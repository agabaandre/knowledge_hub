<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\Region;
use App\Models\Forum;
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

}
