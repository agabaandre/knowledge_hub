<?php
namespace App\View\Composers;

use App\Models\GeoCoverage;
use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Illuminate\View\View;

class GeoAreasViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $geoareas  = cache()->remember('geoareas',$minutes, function () {
            return   GeoCoverage::all();
        });

        $view->with('geoareas',$geoareas);
    }

}

?>