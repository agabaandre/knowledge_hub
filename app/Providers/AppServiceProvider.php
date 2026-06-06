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
use Illuminate\Support\Facades\View;
use App\Models\StaticLink;
use App\Services\HubStorageService;
use App\Support\EmailConfig;

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
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new \App\Translation\MergingTranslationLoader(
                $app['files'],
                $app['path.lang'],
                storage_path('app/ui_translations')
            );
        });

        define('PHPGRID_LIBPATH', 'libs/phpgrid/');
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        try {
            EmailConfig::applyRuntimeConfig();
        } catch (\Throwable $e) {
            // Database may be unavailable during install or early bootstrap.
        }

        try {
            if (Schema::hasTable('hub_storage_settings')) {
                app(HubStorageService::class)->registerDiskConfig();
            }
        } catch (\Throwable $e) {
            // Ignore during install / missing DB.
        }
    }
}
