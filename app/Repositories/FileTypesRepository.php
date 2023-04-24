<?php
namespace App\Repositories;

use App\Models\PublicationType;
use Illuminate\Http\Request;

class FileTypesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas = PublicationType::orderBy('id','desc');

        if($request->term)
        $areas->where('name','like','%'.$request->term.'%');

        $result = $areas->paginate($rows_count);

        return $result;
    }
    

    public function save(Request $request){

        $row = new PublicationType();
        $row->name = $request->name;
        $row->save();

        return $row;
    }


    public function find($id){

        return PublicationType::find($id);
    }

   function delete($id)
    {
        return PublicationType::find($id)->delete();
    }


}