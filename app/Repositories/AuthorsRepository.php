<?php
namespace App\Repositories;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $authors = Author::orderBy('id','desc');
        if($request->term)
        $authors->where('name','like','%'.$request->term.'%');
        $result = $authors ->paginate($rows_count);

        return  $result;
    }
    
    public function save(Request $request){
        $quote = new Author();

        return $quote;
    }

    public function find($id){

        return Author::find($id);
    }

    public function delete($id){

        return Author::find($id)->delete();
    }


}