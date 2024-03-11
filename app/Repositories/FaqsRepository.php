<?php
namespace App\Repositories;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $faqs = Faq::paginate($rows_count);

        return $faqs;
    }
    
    public function save(Request $request){

        $faq = ($request->id)?Faq::find($request->id):new Faq();
        
        $faq->question = $request->question;
        $faq->answer   = $request->answer;

        $save = ($request->id)?$faq->update(): $faq->save();
        return $faq;
    }

    public function find($id){

        return Faq::find($id);
    }

    public function delete($id){

        return Faq::find($id)->delete();
    }


}