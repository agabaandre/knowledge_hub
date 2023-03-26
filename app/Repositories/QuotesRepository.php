<?php
namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuotesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $pubs = Quote::paginate($rows_count);

        return $pubs;
    }
    
    public function save(Request $request){
        $pub = new Quote();

        return $pub;
    }

    public function find($id){

        return Quote::find($id);
    }


}