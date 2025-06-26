<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Illuminate\View\View;

class SubThemesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $subthemes = cache()->remember('subthemes',$minutes, function () {
            return   SubThemeticArea::all();
        });
        
        $view->with('subthemes',$subthemes);
    }

}

?>