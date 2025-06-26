<?php
namespace App\View\Composers;

use App\Models\PublicationCategory;
use Illuminate\View\View;

class PublicationCategoryViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $file_categories = cache()->remember('file_categories',$minutes, function () {
            return   PublicationCategory::all();
        });
        
        $view->with('file_categories',$file_categories);
    }

}

?>