<?php
namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $tags       = Tag::orderBy('id','desc');
        
        if($request->term)
        $tags->where('tag_text','like','%'.$request->term.'%');

        $result = $tags->paginate($rows_count);

        return $result;
    }
    
    public function save(Request $request){
        $tag = new Tag();
        $tag->tag_text = $request->name;
        $tag->save();

        return $tag;
    }

    public function find($id){

        return Tag::find($id);
    }

    public function delete($id){

        return Tag::find($id)->delete();
    }


}