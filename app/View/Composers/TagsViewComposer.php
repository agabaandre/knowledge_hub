<?php
namespace App\View\Composers;

use App\Models\Tag;
use Illuminate\View\View;

class TagsViewComposer{

    public function compose(View $view){

        $tags = Tag::all();
        $view->with('tags',$tags);
    }

}

?>