<?php
namespace App\View\Composers;

use App\Models\Country;
use App\Repositories\SharedRepo;
use Illuminate\View\View;

class CountriesViewComposer{


    protected $sharedRepo;

    public function __construct(SharedRepo $sharedRepo) {
        $this->sharedRepo = $sharedRepo;
    }

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $countries  = cache()->remember('countries',$minutes, function () {
            $query =   Country::where('national','national');
            $this->sharedRepo->access_filter($query,true);
            $countries = $query->get();
            
            return $countries;
        });

        $view->with('countries',$countries);
    }


}

?>