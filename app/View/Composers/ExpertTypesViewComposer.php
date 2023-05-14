<?php
namespace App\View\Composers;

use App\Models\ExpertType;
use App\Models\Tag;
use Illuminate\View\View;

class ExpertTypesViewComposer{

    public function compose(View $view){

        $types = ExpertType::all();
        $view->with('expert_types',$types);
    }

}

?>