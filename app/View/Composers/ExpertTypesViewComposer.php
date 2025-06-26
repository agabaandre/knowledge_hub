<?php
namespace App\View\Composers;

use App\Models\ExpertType;
use App\Models\Tag;
use Illuminate\View\View;

class ExpertTypesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $types  = cache()->remember('types',$minutes, function () {
            return   ExpertType::all();
        });

        $view->with('expert_types',$types);
    }

}

?>