<?php
namespace App\View\Composers;

use App\Models\Fact;
use Illuminate\View\View;

class FactsViewComposer{

    public function compose(View $view){

        $facts = Fact::all();
        $view->with('facts',$facts);
    }

}

?>