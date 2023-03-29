<?php
namespace App\Repositories;

use App\Models\Expert;
use Illuminate\Http\Request;

class ExpertsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = Expert::orderBy('id','desc');
        return $results->paginate($rows_count);
    }
    
    public function save(Request $request){
        $asset = new Expert();
        return $asset;
    }

    public function find($id){

        return Expert::find($id);
    }


}