<?php
namespace App\View\Composers;

use App\Models\DataCategory;
use Illuminate\View\View;

class DataCategoriesViewComposer{

    public function compose(View $view){
        $categories = DataCategory::where('show_on_menu',1)->get();
        $view->with('data_categories',$categories);
    }

}

?>