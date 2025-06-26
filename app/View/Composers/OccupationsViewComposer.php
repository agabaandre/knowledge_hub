<?php
namespace App\View\Composers;

use App\Models\JobTitle;
use Illuminate\View\View;

class OccupationsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $jobs  = cache()->remember( 'occupations',$minutes, function () {
            return   JobTitle::all();
        });
        
        $view->with( 'jobs',$jobs);
    }

}

?>