<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\Question;
use Illuminate\View\View;

class QuestionsViewComposer{

    public function compose(View $view){

        $questions = Question::with('responses','answers')->get();
        $view->with('questions',$questions);
    }

}

?>