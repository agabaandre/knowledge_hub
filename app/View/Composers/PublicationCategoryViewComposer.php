<?php
namespace App\View\Composers;

use App\Models\PublicationCategory;
use Illuminate\View\View;

class PublicationCategoryViewComposer{

    public function compose(View $view){

        $file_categories = PublicationCategory::all();
        $view->with('file_categories',$file_categories);
    }

}

?>