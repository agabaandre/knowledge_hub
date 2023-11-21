<?php
namespace App\View\Composers;

use App\Models\DataCategory;
use Illuminate\View\View;

class DataCategoriesViewComposer{

    public function compose(View $view){
       
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);
        
        $categories  = cache()->remember('categories',$minutes, function () {
            return   DataCategory::with("sub_categories")->where('show_on_menu',1)->get();
        });
    
        $view->with('data_categories',$categories);
    }

}

?>