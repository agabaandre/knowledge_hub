<?php
namespace App\Repositories;

use App\Models\Question;
use Illuminate\Http\Request;

class QuizRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;

        if($request->term)
        $qns = Question::orderBy('id',$request->term);
        
        $result = $qns->paginate($rows_count);

        return $qns;
    }
    
    public function save(Request $request){
        $qn = new Question();
        
        $qn->question_text = $request->question;
        if($request->resource_id)
        $qn->resource_id   = $request->resource_id;
        $qn->save();

        return $qn;
    }

    public function find($id){

        return Question::find($id);
    }


    public function delete($id){

        return Question::find($id)->delete();
    }

    


}