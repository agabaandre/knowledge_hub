<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       $allHelpers = glob(app_path('Helpers').'/*.php');

       foreach ($allHelpers as $key=>$helperFile) {

           require_once $helperFile;
       }

    }

   
}
