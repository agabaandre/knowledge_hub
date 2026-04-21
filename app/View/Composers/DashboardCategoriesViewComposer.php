<?php
namespace App\View\Composers;

use App\Models\DataCategory;
use Illuminate\View\View;

class DashboardCategoriesViewComposer{

    public function compose(View $view){
       
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);
        
        $categories  = cache()->remember('dashboard_categories',$minutes, function () {
            return DataCategory::with(['sub_categories' => function ($q) {
                $q->orderBy('sub_catgeory_name', 'asc')->orderBy('id', 'asc');
            }])
                ->where('is_dashboard', 1)
                ->where('show_on_menu', 0)
                ->orderBy('category_name', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        });
    
        $view->with('dashboard_categories',$categories);

        //dd($categories);
    }

}

?>