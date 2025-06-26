<?php
namespace App\View\Composers;

use App\Models\Tag;
use Illuminate\View\View;

class TagsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $tags = cache()->remember('tags',$minutes, function () {
            return   Tag::all();
        });

        $view->with('tags',$tags);
    }

}

?>