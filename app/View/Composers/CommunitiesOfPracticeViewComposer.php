<?php
namespace App\View\Composers;

use App\Models\CommunityOfPractice;
use App\Models\PublicationType;
use Illuminate\View\View;

class CommunitiesOfPracticeViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $communities  = cache()->remember('communities',$minutes, function () {
            return   CommunityOfPractice::where('is_active',1)->get();
        });
        
        $view->with('communities',$communities);
    }

}

?>