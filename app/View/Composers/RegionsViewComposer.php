<?php
namespace App\View\Composers;

use App\Models\Region;
use Illuminate\View\View;

class RegionsViewComposer{

    public function compose(View $view){

        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $isCountryHub = (function_exists('hub_is_country_portal') && hub_is_country_portal())
            || (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled())
            || (function_exists('states_enabled') && ! states_enabled());
        $ownerCountryId = function_exists('hub_owner_country_id') ? hub_owner_country_id() : null;
        $ownerRegionId = function_exists('hub_owner_region_id') ? hub_owner_region_id() : null;

        if ($isCountryHub && $ownerCountryId) {
            $cacheKey = 'regions_hub_owner_'.($ownerRegionId ?: 'x').'_'.$ownerCountryId;
            $geoareas = cache()->remember($cacheKey, $minutes, function () use ($ownerCountryId, $ownerRegionId) {
                $query = Region::query()->with(['countries' => function ($q) use ($ownerCountryId) {
                    $q->where('id', (int) $ownerCountryId)->orderBy('name', 'asc');
                }])->orderBy('region_name', 'asc');

                if ($ownerRegionId) {
                    $query->where('id', (int) $ownerRegionId);
                }

                return $query->get();
            });

            $view->with('regions', $geoareas);

            return;
        }

        $geoareas = cache()->remember('regions', $minutes, function () {
            return Region::with(['countries' => function ($query) {
                $query->orderBy('name', 'asc');
            }])->orderBy('region_name', 'asc')->get();
        });

        $view->with('regions', $geoareas);
    }

}
