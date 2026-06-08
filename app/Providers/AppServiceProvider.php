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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\StaticLink;
use App\Services\HubStorageService;
use App\Support\AiConfig;
use App\Support\EmailConfig;
use App\Support\LearningConfig;
use App\Support\MapConfig;
use App\Support\SsoConfig;

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

        // Must run in register() so the translator singleton receives the merging loader
        // (boot() is too late — translator keeps the default FileLoader otherwise).
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new \App\Translation\MergingTranslationLoader(
                $app['files'],
                $app['path.lang'],
                storage_path('app/ui_translations')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $appUrl = config('app.url');
        if (is_string($appUrl) && $appUrl !== '') {
            URL::forceRootUrl(rtrim($appUrl, '/'));
        }

        if (! defined('PHPGRID_LIBPATH')) {
            define('PHPGRID_LIBPATH', 'libs/phpgrid/');
        }
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        try {
            EmailConfig::applyRuntimeConfig();
            LearningConfig::applyRuntimeConfig();
            MapConfig::applyRuntimeConfig();
            SsoConfig::applyRuntimeConfig();
            AiConfig::applyRuntimeConfig();
        } catch (\Throwable $e) {
            // Database may be unavailable during install or early bootstrap.
        }

        try {
            $hubStorage = app(HubStorageService::class);
            $hubStorage->ensureHostDataDirectories();
            $hubStorage->ensurePublicStorageSymlink();
            if (Schema::hasTable('hub_storage_settings')) {
                $hubStorage->registerDiskConfig();
            }
        } catch (\Throwable $e) {
            // Ignore during install / missing DB.
        }

        try {
            if (! $this->app->runningInConsole()) {
                \App\Support\LocaleDirection::applyAppLocale();
            }
        } catch (\Throwable $e) {
            // Ignore during install / missing DB.
        }

        View::composer(
            ['admin.adminunits.*', 'adminunits.*'],
            \App\View\Composers\AdminUnitsViewComposer::class
        );
    }
}
