<?php
namespace App\View\Composers;

use App\Models\GeoCoverage;
use App\Models\PublicationType;
use App\Models\Country;
use Illuminate\View\View;

class GeoAreasViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $geoareas  = cache()->remember('geoareas',$minutes, function () {
            return   Country::where('national','national')->get();
        });

        $view->with('geoareas',$geoareas);
    }

}

?>