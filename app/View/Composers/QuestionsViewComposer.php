<?php
namespace App\View\Composers;

use App\Models\PublicationType;
use App\Models\Question;
use Illuminate\View\View;

class QuestionsViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $questions = cache()->remember('questions',$minutes, function () {
            return   Question::with('responses','answers')->whereHas('answers')->get();
        });
        
        $view->with('questions',$questions);
    }

}

?>