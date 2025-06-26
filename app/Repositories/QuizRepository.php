<?php
namespace App\Repositories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionStats;
use Illuminate\Http\Request;

class QuizRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
       
        $qns = Question::orderBy('id','desc');
        
        if($request->term)
        $qns->where('question_text','like','%'.$request->term.'%');
        
        $result = $qns->paginate($rows_count);

        return  $result;
    }
    
    public function save(Request $request){
        $qn = ($request->id)? Question::find($request->id):new Question();
        
        $qn->question_text = $request->question;
        if($request->resource_id)
        $qn->resource_id   = $request->resource_id;
        
        $save = ($request->id)?$qn->update():$qn->save();

        return $qn;
    }

    public function find($id){
        return Question::find($id);
    }

    public function delete($id){

        return Question::find($id)->delete();
    }

    public function save_stat(Request $request){

       $stat = new QuestionStats();

       $answer = Answer::find($request->ans_id);

       $stat->question_id = $request->qn_id;
       $stat->answer_id   = $request->ans_id;
       $stat->is_correct  = $answer->is_correct;
       $stat->user_id     = (@current_user())?current_user()->id:0;

       set_cookie('answered_'.$request->qn_id,1);
       
       return $stat->save();
    }

    public function get_answers($qn_id){

        return Answer::where('question_id',$qn_id)->paginate(5);
    }

    public function save_answer(Request $request){

        $answer = ($request->id)? Answer::find($request->id):new Answer();
        $answer->answer_text = $request->answer;
        $answer->question_id = $request->question_id;
        $answer->is_correct  = ($request->answer_type=='correct')?1:0;
        $answer->answer_explanation = ($request->answer_type=='correct')?$request->explanation:null;
        $saved = ($request->id)?$answer->update():$answer->save();

        return $answer;
    }

    public function delete_answer($id){

        return Answer::find($id)->delete();
    }


    


}