<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AssetTypesRepository;

class AssetTypesController extends Controller
{
    private $assetTypesRepo;

    public function __construct(AssetTypesRepository $assetTypesRepo)
    {
        $this->assetTypesRepo = $assetTypesRepo;
    }

    public function index(Request $request){
        $data['assettypes'] = $this->assetTypesRepo->get($request);
        $data['search'] = (Object) $request->all();
        return view('admin.assettypes.index', $data);
    }

    public function store(Request $request){
        $request->validate([
            'type_name' => 'required'
        ]);

        $saved = $this->assetTypesRepo->save($request);

        if($saved):
            $data = ['message' => 'Asset type saved successfully', 'status' => 'success', 'data' => $saved];
        else:
            $data = ['message' => 'Operation failed, try again', 'status' => 'failure', 'data' => $saved];   
        endif;

        if($request->ajax()){
            return response()->json($data, 200);
        }
        
        return back()->with($data);
    }

    public function destroy(Request $request){
        return $this->assetTypesRepo->delete($request->id);
    }
}

