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
        $faq = new Faq();

        return $faq;
    }

    public function find($id){

        return Faq::find($id);
    }


}