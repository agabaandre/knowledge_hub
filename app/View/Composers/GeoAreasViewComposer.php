<?php
namespace App\View\Composers;

use App\Models\GeoCoverage;
use App\Models\PublicationType;
use App\Models\Country;
use App\Repositories\SharedRepo;
use Illuminate\View\View;

class GeoAreasViewComposer{


    protected $sharedRepo;

    public function __construct(SharedRepo $sharedRepo) {
        $this->sharedRepo = $sharedRepo;
    }

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $geoareas  = cache()->remember('geoareas',$minutes, function () {
            $query =   Country::where('national','national');
            $this->sharedRepo->access_filter($query,true);
            $countries = $query->get();
            
            return $countries;
        });

        $view->with('geoareas',$geoareas);
    }

}

?>