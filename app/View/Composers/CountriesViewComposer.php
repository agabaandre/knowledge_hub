<?php
namespace App\View\Composers;

use App\Models\Country;
use Illuminate\View\View;

class CountriesViewComposer{

    public function compose(View $view){

        $countries = Country::all();
        $view->with('countries',$countries);
    }

}

?>