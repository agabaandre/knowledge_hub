<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExpertType;
use App\Repositories\ExpertsRepository;

class ExpertsAdminController extends Controller
{
    private $expertsRepo;

    public function __construct(ExpertsRepository $expertsRepo)
    {
        $this->expertsRepo = $expertsRepo;
    }

    public function index(Request $request){

        $data['experts'] = $this->expertsRepo->get($request);
        $data['types'] = ExpertType::all();
        $data['countries'] = Country::all();
        $data['jobs'] = JobTitle::all();
        // Get unique ISCO classifications (removes duplicates by isco_id)
        $data['isco_classifications'] = \App\Models\IscoClassification::uniqueByIscoId()->get();
        $data['search']  = (Object) $request->all();
        return view('admin.experts.index',$data);
    }

  
    public function store(Request $request){

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'type_id'=>'required',
            'country_id'=>'required',
        ]);

        try {
            $saved = $this->expertsRepo->save($request);
        } catch (\Throwable $e) {
            \Log::error('Expert save failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Save failed: ' . $e->getMessage(),
                    'status' => 'failure',
                ], 500);
            }
            return back()
                ->withInput()
                ->with('message', 'Save failed. Please check required fields and try again.')
                ->with('status', 'failure');
        }

        $message = $saved ? 'Expert saved successfully' : 'Operation failed, try again';
        $status = $saved ? 'success' : 'failure';

        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
                'status' => $status,
                'data' => $saved ? $saved->only(['id', 'first_name', 'last_name', 'email']) : null,
            ], 200);
        }

        // Do not flash Eloquent models (breaks session serialization → HTTP 500)
        return back()
            ->with('message', $message)
            ->with('status', $status);
    }


    public function destroy(Request $request){
        return $this->expertsRepo->delete($request->id);
    }


    //EXPERT TYPES
    
    public function types(Request $request){

        $data['types'] = $this->expertsRepo->get_types($request);
        $data['search']  = (Object) $request->all();
        return view('admin.experts.types',$data);
    }


    public function save_type(Request $request){

        $request->validate([
            'type'=>'required'
        ]);

        $saved = $this->expertsRepo->save_type($request);

        $message = $saved ? 'Expert type saved successfully' : 'Operation failed, try again';
        $status = $saved ? 'success' : 'failure';

        if ($request->ajax()) {
            return response()->json(['message' => $message, 'status' => $status], 200);
        }

        return back()->with('message', $message)->with('status', $status);
    }


    public function delete_type(Request $request){
        return $this->expertsRepo->delete_type($request->id);
    }

    public function getJobTitlesByIsco(Request $request){
        $iscoId = $request->isco_id;
        
        // If classification_id is provided instead, get the ISCO ID from the classification
        if (!$iscoId && $request->classification_id) {
            $iscoClassification = \App\Models\IscoClassification::find($request->classification_id);
            if ($iscoClassification) {
                $iscoId = $iscoClassification->isco_id;
            }
        }
        
        if ($iscoId) {
            // Find job titles where isco_id matches
            $jobTitles = JobTitle::where('isco_id', $iscoId)->orderBy('name')->get();
            return response()->json($jobTitles);
        }
        
        return response()->json([]);
    }

  
}
