<?php
namespace App\View\Composers;

use App\Models\GeoCoverage;
use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Illuminate\View\View;

class GeoAreasViewComposer{

    public function compose(View $view){

        $geoareas = GeoCoverage::all();
        $view->with('geoareas',$geoareas);
    }

}

?>