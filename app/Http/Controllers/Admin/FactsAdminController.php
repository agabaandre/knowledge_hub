<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RefreshAfricaHealthFactsJob;
use App\Repositories\FactsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FactsAdminController extends Controller
{
    private $factsRepo;

    public function __construct(FactsRepository $factsRepo)
    {
        $this->factsRepo = $factsRepo;
    }

    public function index(Request $request)
    {
        $data['facts'] = $this->factsRepo->get($request);
        $data['search'] = (object) $request->all();
        $data['facts_ai_meta'] = Cache::get('facts_last_ai_refresh');

        return view('admin.facts.index', $data);
    }

    public function refreshOpenAi(Request $request)
    {
        abort_unless($request->user() && $request->user()->can('manage_facts'), 403);

        RefreshAfricaHealthFactsJob::dispatch();

        return back()->with([
            'message' => __('A refresh job has been queued. AI-managed facts update when the queue worker runs (usually within a few minutes). Ensure OPEN_API_KEY is set for OpenAI; otherwise curated fallback facts are used.'),
            'status' => 'success',
        ]);
    }

    public function store(Request $request){

        $request->validate([
            'title'=>'required',
            'summary'=>'required',
            'description'=>'required',
        ]);

        $saved = $this->factsRepo->save($request);

        if($saved):
            $data = ['message'=>'Fact saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }


    public function destroy(Request $request){
        return $this->factsRepo->delete($request->id);
    }



}
