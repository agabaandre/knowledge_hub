<?php
namespace App\View\Composers;

use App\Models\Country;
use App\Repositories\SharedRepo;
use Illuminate\View\View;

class CountriesViewComposer{


    protected $sharedRepo;

    public function __construct(SharedRepo $sharedRepo) {
        $this->sharedRepo = $sharedRepo;
    }

    public function compose(View $view){

        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $isCountryHub = (function_exists('hub_is_country_portal') && hub_is_country_portal())
            || (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled())
            || (function_exists('states_enabled') && ! states_enabled());
        $ownerCountryId = function_exists('hub_owner_country_id') ? hub_owner_country_id() : null;

        // Member-state hubs only expose the configured owner country in country pickers.
        if ($isCountryHub && $ownerCountryId) {
            $cacheKey = 'countries_hub_owner_'.$ownerCountryId;
            $countries = cache()->remember($cacheKey, $minutes, function () use ($ownerCountryId) {
                return Country::query()
                    ->where('id', (int) $ownerCountryId)
                    ->orderBy('name', 'asc')
                    ->get();
            });

            $view->with('countries', $countries);
            $view->with('hubCountriesScoped', true);

            return;
        }

        $countries = cache()->remember('countries', $minutes, function () {
            $query = Country::where('national', 'national');
            $this->sharedRepo->access_filter($query, true);
            return $query->orderBy('name', 'asc')->get();
        });

        $view->with('countries', $countries);
        $view->with('hubCountriesScoped', false);
    }


}
