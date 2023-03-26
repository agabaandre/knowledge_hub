<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\Question;
use Illuminate\View\View;

class QuestionsViewComposer{

    public function compose(View $view){

        $file_types = Question::all();
        $view->with('questions',$file_types);
    }

}

?>