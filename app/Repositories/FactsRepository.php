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

        $fact = ($request->id)?Fact::find($request->id):new Fact();
        $fact->fact_title = $request->title;
        $fact->fact_summary = $request->summary;
        $fact->fact_description = $request->description;

        return ($request->id)?$fact->update():$fact->save();
    }

    public function find($id){

        return Fact::find($id);
    }

    public function delete($id){

        return Fact::find($id)->delete();
    }


}