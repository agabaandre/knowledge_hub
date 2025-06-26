<?php
namespace App\View\Composers;

use Illuminate\View\View;

class AdminStatsViewComposer{

    public function compose(View $view){

        $data['pending_forum_comments'] = [];
        $data['pending_publication_comments'] = [];
        $data['pending_publication_comments_count'] =0;
        $data['pending_forum_comments_count'] =0;

        $view->with($data);
    }

}

?>