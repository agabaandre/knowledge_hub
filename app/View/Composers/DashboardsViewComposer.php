<?php
namespace App\View\Composers;

use App\Models\Publication;
use Illuminate\View\View;

class DashboardsViewComposer{

    public function compose(View $view)
    {
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);
        
        $dashboards = cache()->remember('dashboards', $minutes, function () {
            // Show only items that are admin-only access, and for the menu specifically
            // constrain to the Dashboard category (publication_catgory_id = 6)
            return Publication::where('is_admin_only_access', 1)
                ->where('publication_catgory_id', 6)
                ->orderBy('id','desc')
                ->get();
        });
    
        $view->with('dashboards', $dashboards);
    }

}

?>