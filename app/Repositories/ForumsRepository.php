<?php
namespace App\Repositories;

use App\Models\Faq;
use App\Models\Forum;
use Illuminate\Http\Request;

class ForumsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $faqs = Forum::paginate($rows_count);

        return $faqs;
    }
    
    public function save(Request $request){
        $faq = new Faq();

        return $faq;
    }

    public function find($id){

        return Forum::find($id);
    }


}