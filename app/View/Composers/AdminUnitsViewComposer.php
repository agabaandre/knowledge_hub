<?php
namespace App\View\Composers;

use App\Models\AdministrativeUnit;
use App\Models\Author;
use Illuminate\View\View;

class AdminUnitsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $adminunits = cache()->remember('adminunits',$minutes, function () {
            return  AdministrativeUnit::all();
        });
    
        $view->with('adminunits',$adminunits);
    }

}

?>