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
            $query = Country::query()
                ->where('region_id', '>', 0)
                ->whereNotNull('iso_code')
                ->where('iso_code', '!=', '')
                ->orderBy('name');

            // Country hubs still list AU states for optional mapping, but default selection is the owner.
            return $query->get(['id', 'name', 'iso_code', 'iso3_code']);
        });

        if (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled()
            && function_exists('hub_owner_country_id') && hub_owner_country_id()) {
            $ownerId = (int) hub_owner_country_id();
            $countries = $countries->sortBy(function ($c) use ($ownerId) {
                return (int) $c->id === $ownerId ? 0 : 1;
            })->values();
        }

        // Parent dropdowns need the full tree. Do NOT overwrite controller `$adminunits`
        // (paginator / filtered list) on admin index pages.
        $view->with('allAdminUnits', $allAdminUnits);
        $view->with('hubCountries', $countries);

        if (! $view->offsetExists('adminunits')) {
            $view->with('adminunits', $allAdminUnits);
        }
    }
}
