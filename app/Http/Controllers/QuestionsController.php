<?php

namespace App\Http\Controllers;

use App\Repositories\QuizRepository;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    private $quizRepo;

    public function __construct(QuizRepository $quizRepo)
    {
        $this->quizRepo = $quizRepo;
    }

    public function save_stats(Request $request){

        $saved = $this->quizRepo->save_stat($request);
        return response(['success'=>$saved],200);
    }

    
}
