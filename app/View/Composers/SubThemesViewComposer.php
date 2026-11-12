<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Illuminate\View\View;

class SubThemesViewComposer{

    public function compose(View $view){

        $minutes = (int) config('khub.cache_expiry_minutes', 60 * 24);

        $subthemes = cache()->remember('subthemes',$minutes, function () {
            return   SubThemeticArea::orderBy('description', 'asc')->get();
        });
        
        $view->with('subthemes',$subthemes);
    }

}

?>