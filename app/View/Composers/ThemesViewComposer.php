<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use Illuminate\View\View;

class ThemesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        // Publication wizard & forms: A–Z. (Homepage uses HomeController + ThemesRepository::get → display order.)
        $subthemes = cache()->remember('themes_form_alphabetical', $minutes, function () {
            return ThemeticArea::with(['subthemes' => function ($query) {
                $query->orderBy('description', 'asc');
            }])->orderBy('description', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        });

        $view->with('themes',$subthemes);
    }

}

?>