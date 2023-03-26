<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use Illuminate\View\View;

class FileTypesViewComposer{

    public function compose(View $view){

        $file_types = PublicationType::all();
        $view->with('file_types',$file_types);
    }

}

?>