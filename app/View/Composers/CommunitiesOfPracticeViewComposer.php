<?php
namespace App\View\Composers;

use App\Models\CommunityOfPractice;
use App\Models\PublicationType;
use Illuminate\View\View;

class CommunitiesOfPracticeViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $communities = cache()->remember('communities', $minutes, function () {
            return CommunityOfPractice::where('is_active', 1)->orderBy('community_name', 'asc')->get();
        });

        if (! user_email_allows_africa_cdc_staff_community(auth()->user())) {
            $communities = $communities->filter(function ($c) {
                return ! community_is_africa_cdc_staff_restricted($c);
            })->values();
        }

        $view->with('communities', $communities);
    }

}

?>