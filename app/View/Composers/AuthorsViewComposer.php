<?php
namespace App\View\Composers;

use App\Models\Author;
use Illuminate\View\View;

class AuthorsViewComposer{

    public function compose(View $view){

        $authors = Author::all();
        $view->with('authors',$authors);
    }

}

?>