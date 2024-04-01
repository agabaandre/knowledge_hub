<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;

class ResourcesController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }

    public function index(Request $request){
        $request['is_admin']  = 1;
        $data['publications'] = $this->publicationsRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.publications.index',$data);
    }

    public function create(Request $request){

        $data['publication'] = null;
        return view('admin.publications.create',$data);
    }

    public function edit(Request $request){

        $publication          =  $this->publicationsRepo->find($request->id);
        $publication->tag_ids = array_column($publication->tags->toArray(),'tag_id');

        $data['publication'] = $publication;

      //  dd($publication);
       
        return view('admin.publications.create',$data);
    }

    public function details(Request $request){

        $publication          =  $this->publicationsRepo->find($request->id);
        $data['publication']  = $publication;       
        return view('admin.publications.details',$data);
    }


    public function summaries(Request $request){

        $data['summaries']        =  $this->publicationsRepo->get_summaries($request);    
        return view('admin.publications.summaries',$data);
    }
    
    public function summary(Request $request){

        $summary          =  $this->publicationsRepo->find_summary($request);
        $data['summary']  = $summary;       
        return view('admin.publications.summary',$data);
    }

    public function store(Request $request){

        $saved = $this->publicationsRepo->save($request);

        if($saved):
            $data = ['message'=>'Resource saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back(200)->with($data);
    }

    public function approval(Request $request){

        $saved   = $this->publicationsRepo->change_approval_status($request);

        if($saved):
            $data = ['message'=>'Resource updated successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        return back()->with($data);
    }

    public function summary_approval(Request $request){

        $saved   = $this->publicationsRepo->change_approval_status($request);

        if($saved):
            $data = ['message'=>'Resource updated successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        return back()->with($data);
    }
    public function moderate(Request $request){

        $data['publications'] = $this->publicationsRepo->with_pending_comments($request);
        $data['search']       = (Object) $request->all();
        return view('admin.publications.moderate',$data);
    }

    public function destroy(Request $request){

        return $this->publicationsRepo->delete($request->id);
    }

    public function approve_comment(Request $request){
        return $this->publicationsRepo->approve_comment($request->id);
    }


    public function reject_comment(Request $request){
        return $this->publicationsRepo->reject_comment($request->id);
    }

  
}
