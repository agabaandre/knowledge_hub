<?php
namespace App\Repositories;

use App\Models\Tag;
use App\View\Composers\TagsViewComposer;
use Illuminate\Http\Request;

class TagsRepository{

    public function get(Request $request, $return_array = false){
        \Log::info('TagsRepository@get called', ['return_array' => $return_array]);
        $rows_count = ($request->rows)?$request->rows:20;
        $tags       = Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc');

        if($request->term)
        $tags->where('tag_text','like','%'.$request->term.'%');

        $result = ($return_array)?$tags->get():$tags->paginate($rows_count);
        \Log::info('TagsRepository@get result type', ['class' => get_class($result)]);
        return $result;
    }

    public function save(Request $request){
        $tag = new Tag();
        $tag->tag_text = $request->name;
        if ($request->has('is_health_topic')) {
            $tag->is_health_topic = $request->is_health_topic ? 1 : 0;
        } else {
            $tag->is_health_topic = 1; // default yes
        }
        if ($request->has('is_health_emergency')) {
            $tag->is_health_emergency = $request->is_health_emergency ? 1 : 0;
        }
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        $tag->save();
        TagsViewComposer::forgetTagListCache();

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

        $tag->tag_text = $request->tag_text;
        if ($request->has('is_health_topic')) {
            $tag->is_health_topic = $request->is_health_topic ? 1 : 0;
        }
        if ($request->has('is_health_emergency')) {
            $tag->is_health_emergency = $request->is_health_emergency ? 1 : 0;
        }
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        $tag->save();
        TagsViewComposer::forgetTagListCache();

        return $tag;
    }

    public function delete($id){

        $deleted = Tag::find($id)?->delete();
        TagsViewComposer::forgetTagListCache();

        return $deleted;
    }


}
