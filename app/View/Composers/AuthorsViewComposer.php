<?php
namespace App\View\Composers;

use App\Models\Author;
use Illuminate\View\View;

class AuthorsViewComposer{

    public function compose(View $view){

        $minutes = (int) config('khub.cache_expiry_minutes', 60 * 24);

        $authors = cache()->remember('authors',$minutes, function () {
            return  Author::orderBy('name', 'asc')->get();
        });
    
        $view->with('authors',$authors);
    }

}

?>