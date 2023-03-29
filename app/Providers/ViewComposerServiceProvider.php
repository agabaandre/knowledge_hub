<?php

namespace App\Providers;

use App\View\Composers\FactsViewComposer;
use App\View\Composers\FileTypesViewComposer;
use App\View\Composers\GeoAreasViewComposer;
use App\View\Composers\QuestionsViewComposer;
use App\View\Composers\SubThemesViewComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        
        View::composer(['home/partials/*','partials/publications/*'],FileTypesViewComposer::class);
        View::composer(['partials/quiz/*'],QuestionsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],FactsViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],SubThemesViewComposer::class);
        View::composer(['publications/partials/*','partials/publications/*'],GeoAreasViewComposer::class);
        
    }
}
