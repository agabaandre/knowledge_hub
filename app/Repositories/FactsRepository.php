<?php
namespace App\Repositories;

use App\Models\Fact;
use Illuminate\Http\Request;

class FactsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $facts = Fact::paginate($rows_count);

        return $facts;
    }
    
    public function save(Request $request){
        $fact = new Fact();

        return $fact;
    }

    public function find($id){

        return Fact::find($id);
    }


}