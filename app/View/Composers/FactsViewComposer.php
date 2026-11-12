<?php
namespace App\View\Composers;

use App\Models\Fact;
use Illuminate\View\View;

class FactsViewComposer{

    public function compose(View $view){

        $minutes = (int) config('khub.cache_expiry_minutes', 60 * 24);

        $facts  = cache()->remember('facts',$minutes, function () {
            return   Fact::all();
        });
        
        $view->with('facts',$facts);
    }

}

?>