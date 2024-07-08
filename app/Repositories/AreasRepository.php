<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use Illuminate\Http\Request;

class AreasRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas = GeoCoverage::orderBy('id','desc');

        if($request->term)
        $areas->where('name','like','%'.$request->term.'%');

        $result = $areas->paginate($rows_count);

        return $result;
    }


    public function find($id){

        return Author::find($id);
    }

    public function member_states(){
        return Country::where('region_id','>',0)
        ->withCount('publications as resources')
        ->orderBy('name','asc')->get();
    }

    public function member_state($id){
        return Country::where('id',$id)->first();
    }

    public function countries(){

        return Country::all();
    }


    public function count(){
        return count(GeoCoverage::all());
    }

    public function save(Request $request) {
        $id = $request->input('area_id');
        $name = $request->input('area_name');

        $area = GeoCoverage::findOrNew($id);
        $area->name = $name;
        $area->save();

        return $area ?? null;
    }


}
