<?php
namespace App\View\Composers;

use App\Models\Author;
use Illuminate\View\View;

class AuthorsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $authors = cache()->remember('authors',$minutes, function () {
            return  Author::all();
        });
    
        $view->with('authors',$authors);
    }

}

?>