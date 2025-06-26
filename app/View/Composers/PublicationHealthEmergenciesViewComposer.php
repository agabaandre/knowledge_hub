<?php
namespace App\View\Composers;

use App\Models\Publication;
use App\Models\PublicationTag;
use Illuminate\View\View;

class PublicationHealthEmergenciesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $publications = cache()->remember('health_emergencies',$minutes, function () {
            $taggedpubs = PublicationTag::whereHas('tag', function ($query) {
                                $query->where('is_health_emergency', true);
                            })->pluck('publication_id');

            $publications = Publication::whereIn('id', $taggedpubs)->take(4)->get();
            return   $publications;
        });
        
        $view->with('health_emergencies',$publications);
    }

}

?>