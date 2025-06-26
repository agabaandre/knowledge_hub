<?php
namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagsRepository{

    public function get(Request $request, $return_array = false){
        \Log::info('TagsRepository@get called', ['return_array' => $return_array]);
        $rows_count = ($request->rows)?$request->rows:20;
        $tags       = Tag::orderBy('id','desc');

        if($request->term)
        $tags->where('tag_text','like','%'.$request->term.'%');

        $result = ($return_array)?$tags->get():$tags->paginate($rows_count);
        \Log::info('TagsRepository@get result type', ['class' => get_class($result)]);
        return $result;
    }

    public function save(Request $request){
        $tag = new Tag();
        $tag->tag_text = $request->name;
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        $tag->save();

        return $tag;
    }

    public function find($id){

        return Tag::find($id);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return null; // Or handle the case where tag is not found
        }

        $tag->tag_text = $request->tag_text; // Assuming 'name' is the field name from your form
        $tag->is_health_emergency = ($request->is_health_emergency) ? true:false;
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        $tag->save();

        return $tag;
    }

    public function delete($id){

        return Tag::find($id)->delete();
    }


}
