<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\AccessLog;
use Illuminate\Http\Request;

class LogsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas      = AccessLog::orderBy('id','desc');

        if($request->term)
        $areas->where('country','like','%'.$request->term.'%');

        $result = $areas->paginate($rows_count);
        return $result;
    }
    
    public function count(){
        return count(AccessLog::all());
    }

}