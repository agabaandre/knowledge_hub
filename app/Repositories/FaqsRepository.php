<?php
namespace App\Repositories;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqsRepository{

    public function get(Request $request){
        $rows_count = ($request->rows) ? $request->rows : 20;
        $faqs = Faq::query();

        // Add search functionality
        if ($request->term) {
            $term = $request->term;
            $faqs->where(function($query) use ($term) {
                $query->where('question', 'like', '%' . $term . '%')
                      ->orWhere('answer', 'like', '%' . $term . '%');
            });
        }

        $faqs->orderBy('id', 'desc');
        
        return $faqs->paginate($rows_count)->appends($request->all());
    }
    
    public function save(Request $request){

        $faq = ($request->id)?Faq::find($request->id):new Faq();
        
        // Clean unicode characters from question and answer
        $faq->question = clean_unicode($request->question);
        $faq->answer   = clean_unicode($request->answer);

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