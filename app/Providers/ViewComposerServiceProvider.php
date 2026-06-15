<?php

namespace App\Providers;

use App\View\Composers\AccessGroupsViewComposer;
use App\View\Composers\AdminStatsViewComposer;
use App\View\Composers\AdminUnitsViewComposer;
use App\View\Composers\AssetTypesViewComposer;
use App\View\Composers\AuthorsViewComposer;
use App\View\Composers\CountriesViewComposer;
use App\View\Composers\DashboardCategoriesViewComposer;
use App\View\Composers\DataCategoriesViewComposer;
use App\View\Composers\ExpertTypesViewComposer;
use App\View\Composers\FactsViewComposer;
use App\View\Composers\FileTypesViewComposer;
use App\View\Composers\GeoAreasViewComposer;
use App\View\Composers\PublicationCategoryViewComposer;
use App\View\Composers\QuestionsViewComposer;
use App\View\Composers\RegionsViewComposer;
use App\View\Composers\SubThemesViewComposer;
use App\View\Composers\TagsViewComposer;
use App\View\Composers\ThemesViewComposer;
use App\View\Composers\CommunitiesOfPracticeViewComposer;
use App\View\Composers\DashboardsViewComposer;
use App\View\Composers\OccupationsViewComposer;
use App\View\Composers\PublicationHealthEmergenciesViewComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades;
use App\Models\StaticLink;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['home/partials/*','partials/publications/*','partials/search/*','account/*','admin/publications/*','publications/search'],FileTypesViewComposer::class);
        View::composer(['home/partials/*','partials/publications/*','partials/search/*','account/*','admin/publications/*','publications/search'],PublicationCategoryViewComposer::class);
        View::composer(['partials/quiz/*'],QuestionsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],FactsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],SubThemesViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],GeoAreasViewComposer::class);
        View::composer(['partials/authors/*'],AuthorsViewComposer::class);
        View::composer(['partials/tags/*','publications/*'],TagsViewComposer::class);
        View::composer(['partials/countries/*','dashboards/*','datarecords/*','admin/commsofpractice/*'],CountriesViewComposer::class);
        View::composer(['partials/experts/*'],ExpertTypesViewComposer::class);
        View::composer(['partials/regions/*','partials/search/*','dashboards/*','admin/commsofpractice/*','account/*'],RegionsViewComposer::class);
        View::composer(['partials/publications/*','partials/search/*','account/*'],ThemesViewComposer::class);

        View::composer(['partials/adminunits/*'],AdminUnitsViewComposer::class);

        Facades\View::composer(['admin/*', 'admin.layouts.main_nifty'], AdminStatsViewComposer::class);
        Facades\View::composer('*',AssetTypesViewComposer::class);
        Facades\View::composer('*',DataCategoriesViewComposer::class);
        Facades\View::composer('*',DashboardCategoriesViewComposer::class);
        Facades\View::composer(['admin/*', 'admin.layouts.partials.nav', 'admin.layouts.main_nifty', 'admin.layouts.partials.nifty_sidebar'], DashboardsViewComposer::class);
     
        View::composer(['partials/publications/*','account/*',],CommunitiesOfPracticeViewComposer::class);
        View::composer(['partials/publications/*','account/*',],AccessGroupsViewComposer::class);
        View::composer(['partials/jobs/*'],OccupationsViewComposer::class);
        Facades\View::composer('*',TagsViewComposer::class);
        Facades\View::composer('*',PublicationHealthEmergenciesViewComposer::class);

        View::composer('layouts.*', function ($view) {

            $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);
            $static_links = cache()->remember('adminunits',$minutes, function () {
                return  StaticLink::orderBy('order')->get();
              });
            
            $view->with('staticLinks',$static_links);
        });

        
    }
}
