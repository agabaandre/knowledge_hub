<?php
namespace App\View\Composers;

use App\Models\Publication;
use Illuminate\View\View;

class DashboardsViewComposer{

    public function compose(View $view)
    {
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);
        
        $dashboards = cache()->remember('dashboards', $minutes, function () {
            return Publication::whereHas('data_category', function ($query) {
                $query->where('is_dashboard', 1);
            })->get();
        });
    
        $view->with('dashboards', $dashboards);
    }

}

?>