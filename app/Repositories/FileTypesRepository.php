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

        // dd($request->all());

        // Check if ID is provided to determine if it's an edit or create operation
        if ($request->has('id')) {
            // Editing an existing publication type
            $row = PublicationType::find($request->id);
    
            // If the ID is provided but the publication type is not found, return an error response
            if (!$row) {
                return null;
            }
        } else {
            // Creating a new publication type
            $row = new PublicationType();
        }
    
        // Update or set attributes
        $row->name = $request->name;
        $row->icon = $request->icon;
    
        // Convert 'is_downloadable' value to boolean before assigning
        $row->is_downloadable = $request->downloadable == 1 ? true : false;
    
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