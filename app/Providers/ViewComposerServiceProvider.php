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
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades;

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
        View::composer(['home/partials/*','partials/publications/*','account/*','admin/publications/*'],FileTypesViewComposer::class);
        View::composer(['home/partials/*','partials/publications/*','account/*','admin/publications/*'],PublicationCategoryViewComposer::class);
        View::composer(['partials/quiz/*'],QuestionsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],FactsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],SubThemesViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],GeoAreasViewComposer::class);
        View::composer(['partials/authors/*'],AuthorsViewComposer::class);
        View::composer(['partials/tags/*','publications/*'],TagsViewComposer::class);
        View::composer(['partials/countries/*','dashboards/*','datarecords/*'],CountriesViewComposer::class);
        View::composer(['partials/experts/*'],ExpertTypesViewComposer::class);
        View::composer(['partials/regions/*','partials/search/*','dashboards/*'],RegionsViewComposer::class);
        View::composer(['partials/publications/*','partials/search/*','account/*'],ThemesViewComposer::class);

        View::composer(['partials/adminunits/*'],AdminUnitsViewComposer::class);

        Facades\View::composer('admin/*',AdminStatsViewComposer::class);
        Facades\View::composer('*',AssetTypesViewComposer::class);
        Facades\View::composer('*',DataCategoriesViewComposer::class);
        Facades\View::composer('*',DashboardCategoriesViewComposer::class);

        View::composer(['partials/publications/*'],CommunitiesOfPracticeViewComposer::class);
        View::composer(['partials/publications/*'],AccessGroupsViewComposer::class);
        
    }
}
