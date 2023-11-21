<?php
namespace App\View\Composers;

use App\Models\Country;
use Illuminate\View\View;

class CountriesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);
        
        $countries  = cache()->remember('countries',$minutes, function () {
            return   Country::where("national","National")->get();
        });
    
        $view->with('countries',$countries);
    }

}

?>