<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use Illuminate\View\View;

class ThemesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $subthemes = cache()->remember('themes',$minutes, function () {
            return   ThemeticArea::with('subthemes')->get();
        });

        $view->with('themes',$subthemes);
    }

}

?>