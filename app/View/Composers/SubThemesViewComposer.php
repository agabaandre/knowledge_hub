<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Illuminate\View\View;

class SubThemesViewComposer{

    public function compose(View $view){

        $subthemes = SubThemeticArea::all();
        $view->with('subthemes',$subthemes);
    }

}

?>