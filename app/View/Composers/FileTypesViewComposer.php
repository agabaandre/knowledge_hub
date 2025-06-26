<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use Illuminate\View\View;

class FileTypesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $file_types  = cache()->remember('file_types',$minutes, function () {
            return   PublicationType::all();
        });
        
        $view->with('file_types',$file_types);
    }

}

?>