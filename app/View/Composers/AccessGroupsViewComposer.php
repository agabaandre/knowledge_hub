<?php
namespace App\View\Composers;

use App\Models\CommunityOfPractice;
use App\Models\PublicationType;
use App\Models\UserAccessGroup;
use Illuminate\View\View;

class AccessGroupsViewComposer{

    public function compose(View $view){

        $minutes = (int) config('khub.cache_expiry_minutes', 60 * 24);

        $groups  = cache()->remember('accessgroups',$minutes, function () {
            return   UserAccessGroup::all();
        });
        
        $view->with('accessgroups',$groups);
    }

}

?>