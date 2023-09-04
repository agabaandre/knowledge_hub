<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\QuizRepository;

class QuizController extends Controller
{
    private $quizRepo;

    public function __construct(QuizRepository $quizRepo)
    {
        $this->quizRepo = $quizRepo;
    }

    public function index(Request $request){

        $data['questions'] = $this->quizRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.quiz.index',$data);
    }


    public function store(Request $request){

        $saved = $this->quizRepo->save($request);

        if($saved):
            $data = ['message'=>'Quiz saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back(200)->with($data);
    }
 
    public function destroy(Request $request){

        return $this->quizRepo->delete($request->id);
    }

    public function answers(Request $request){

        if(!$request->qn)
         return redirect()->back();

        $data['question'] = $this->quizRepo->find($request->qn);
        $data['answers']  = $this->quizRepo->get_answers($request->qn);
        return view('admin.quiz.answers',$data);
    }

  
}
