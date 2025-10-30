<?php
namespace App\View\Composers;

use App\Models\Region;
use Illuminate\View\View;

class RegionsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $geoareas = cache()->remember('regions',$minutes, function () {
            return   Region::with(['countries' => function($query) {
                $query->orderBy('name', 'asc');
            }])->orderBy('region_name', 'asc')->get();
        });

        $view->with('regions',$geoareas);
    }

}

?>