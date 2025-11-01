<?php
namespace App\View\Composers;

use App\Models\Publication;
use Illuminate\View\View;

class DashboardsViewComposer{

    public function compose(View $view)
    {
        // Don't cache to ensure fresh data
        // Show admin-only dashboards: publications whose data category is marked as dashboard
        // or publications that are marked as admin-only access
        try {
            $dashboards = Publication::where(function($q){
                $q->whereHas('data_category', function($dq){
                    $dq->where('is_dashboard', 1);
                })
                ->orWhere('is_admin_only_access', 1);
            })
            ->orderBy('id','desc')
            ->get();
        } catch(\Throwable $e) {
            $dashboards = collect();
        }
    
        $view->with('dashboards', $dashboards);
    }

}

?>