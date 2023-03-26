<?php
namespace App\Repositories;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $quotes = Author::paginate($rows_count);

        return $quotes;
    }
    
    public function save(Request $request){
        $quote = new Author();

        return $quote;
    }

    public function find($id){

        return Author::find($id);
    }


}