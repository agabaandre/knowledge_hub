<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TagsRepository;

class TagsController extends Controller
{
    private $tagsRepo;

    public function __construct(TagsRepository $tagsRepo)
    {
        $this->tagsRepo = $tagsRepo;
    }

    public function index(Request $request){

        $data['all_tags'] = $this->tagsRepo->get($request,false);
        $data['allTagsForMapping'] = $this->tagsRepo->allTagsForMapping();
        $data['search']    = (Object) $request->all();
        return view('admin.tags.index',$data);
    }
    
    public function store(Request $request){

        $saved = $this->tagsRepo->save($request);

        if($saved):
            $data = ['message'=>'File type saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }

    public function update(Request $request)
    {
        $updated = $this->tagsRepo->update($request, $request->input('tag_id'));

        $data = $updated
            ? ['message' => 'Tag saved successfully', 'status' => 'success', 'data' => $updated]
            : ['message' => 'Tag could not be saved', 'status' => 'failure', 'data' => null];

        if($request->ajax()){
            return response($data,200);
        }

        notify()->success('Laravel Notify is awesome!');

        return back()->with($data);
    }



    public function destroy(Request $request){
        $user = auth()->user();
        if (! $user || (! $user->can('delete_publication_metadata') && ! $user->can('delete_meta_data'))) {
            return response()->json(['status' => 'failure', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:tags,id',
            'replacement_tag_id' => 'required|integer|exists:tags,id|different:id',
        ]);

        $result = $this->tagsRepo->deleteTagWithMapping((int) $request->id, (int) $request->replacement_tag_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }


}
