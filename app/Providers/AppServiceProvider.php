<?php

namespace App\Providers;

use App;
use App\Repositories\SharedRepo;
use App\Services\AIModel;
use App\Services\ChatGPTService;
use App\Services\ChatPDFService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
           // $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            //$this->app->register(TelescopeServiceProvider::class);
        }

        App::singleton('chatgpt', function ($app) {
            return new ChatGPTService();
        });

        App::singleton('chatpdf', function ($app) {
            return new ChatPDFService();
        });


        $this->app->bind(SharedRepo::class, function ($app) {
            return new SharedRepo();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

       
        
        
    }
}
