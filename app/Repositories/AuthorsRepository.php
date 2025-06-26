<?php
namespace App\Repositories;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $authors = Author::orderBy('id','desc');


        if($request->term)
        $authors->where('name','like','%'.$request->term.'%');

        //Access levels effect to query results
        $this->access_filter($authors);

        $result = $authors ->paginate($rows_count);

        return  $result;
    }
    
    public function save($name){
        
        $author = new Author();
        $author->name = $name;
        $author->save();

        return $author;
    }

    public function find($id){

        return Author::find($id);
    }

    public function delete($id){

        return Author::find($id)->delete();
    }

    public function count(){
        return count(Author::all());
    }


}