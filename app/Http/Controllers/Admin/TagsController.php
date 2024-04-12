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

        $data['tags'] = $this->tagsRepo->get($request);
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

        if($updated) {
            $data = ['message'=>'File type saved successfully','status'=>'success','data'=>$updated];
        }

        if($request->ajax()){
            return response($data,200);
        }

        notify()->success('Laravel Notify is awesome!');

        return back()->with($data);
    }



    public function destroy(Request $request){
        return $this->tagsRepo->delete($request->id);
    }


}
