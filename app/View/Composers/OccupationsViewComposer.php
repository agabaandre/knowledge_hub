<?php
namespace App\View\Composers;

use App\Repositories\ExpertsRepository;
use Illuminate\View\View;

class OccupationsViewComposer{

    public function compose(View $view){

        $jobs = app(ExpertsRepository::class)->getAllJobTitlesCached();

        $view->with( 'jobs',$jobs);
    }

}

?>