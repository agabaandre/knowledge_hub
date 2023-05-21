<?php
namespace App\View\Composers;

use App\Models\Region;
use Illuminate\View\View;

class RegionsViewComposer{

    public function compose(View $view){

        $geoareas = Region::with('countries')->get();
        $view->with('regions',$geoareas);
    }

}

?>