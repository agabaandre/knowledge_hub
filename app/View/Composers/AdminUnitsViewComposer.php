<?php
namespace App\View\Composers;

use App\Models\AdministrativeUnit;
use App\Models\Country;
use Illuminate\View\View;

class AdminUnitsViewComposer
{
    public function compose(View $view)
    {
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $adminunits = cache()->remember('adminunits', $minutes, function () {
            return AdministrativeUnit::all();
        });

        $countries = cache()->remember('admin_unit_countries', $minutes, function () {
            return Country::query()
                ->where('region_id', '>', 0)
                ->whereNotNull('iso_code')
                ->where('iso_code', '!=', '')
                ->orderBy('name')
                ->get(['id', 'name', 'iso_code', 'iso3_code']);
        });

        $view->with('adminunits', $adminunits);
        $view->with('hubCountries', $countries);
    }
}
