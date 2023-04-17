<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\GeoCoverage;
use Illuminate\Http\Request;

class AreasRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas = GeoCoverage::paginate($rows_count);

        return $areas;
    }
    

    public function find($id){

        return Author::find($id);
    }


}