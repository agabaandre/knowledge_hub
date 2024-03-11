<?php
namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuotesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $quotes = Quote::orderBy('id','desc');

        if($request->term)
        $quotes->where('quote','like','%'.$request->term.'%');
        
        $result = $quotes->paginate($rows_count);

        return $result;
    }
    
    public function save(Request $request){

        $quote = ($request->id)?Quote::find($request->id):new Quote();
        $quote->quote = $request->quote;
        $save=  ($request->id)? $quote->update():$quote->save();

        return $quote;
    }

    public function find($id){

        return Quote::find($id);
    }


    public function delete($id){

        return Quote::find($id)->delete();
    }

}