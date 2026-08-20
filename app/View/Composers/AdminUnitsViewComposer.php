<?php
namespace App\View\Composers;

use App\Models\AdministrativeUnit;
use App\Models\Country;
use Illuminate\View\View;

class AdminUnitsViewComposer
{
    public function compose(View $view)
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $allAdminUnits = cache()->remember('adminunits', $minutes, function () {
            return AdministrativeUnit::query()
                ->with('parent')
                ->orderBy('name')
                ->orderBy('id')
                ->get();
        });

        $countries = cache()->remember('admin_unit_countries', $minutes, function () {
            return Country::query()
                ->where('region_id', '>', 0)
                ->whereNotNull('iso_code')
                ->where('iso_code', '!=', '')
                ->orderBy('name')
                ->get(['id', 'name', 'iso_code', 'iso3_code']);
        });

        // Parent dropdowns need the full tree. Do NOT overwrite controller `$adminunits`
        // (paginator / filtered list) on admin index pages.
        $view->with('allAdminUnits', $allAdminUnits);
        $view->with('hubCountries', $countries);

        if (! $view->offsetExists('adminunits')) {
            $view->with('adminunits', $allAdminUnits);
        }
    }
}
