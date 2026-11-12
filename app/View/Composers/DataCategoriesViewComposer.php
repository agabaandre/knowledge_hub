<?php
namespace App\View\Composers;

use App\Models\DataCategory;
use Illuminate\View\View;

class DataCategoriesViewComposer{

    public function compose(View $view){
       
        $minutes = (int) config('khub.cache_expiry_minutes', 60 * 24);
        
        $categories  = cache()->remember('categories',$minutes, function () {
            return   DataCategory::with(["sub_categories" => function($query) {
                $query->orderBy('sub_catgeory_name', 'asc');
            }])->where('show_on_menu',1)->orderBy('category_name', 'asc')->get();
        });
    
        $view->with('data_categories',$categories);
    }

}

?>