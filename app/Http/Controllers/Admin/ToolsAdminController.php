<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\ToolsRepository;
use App\Http\Controllers\Controller;
use App\Models\ToolCategory;
use App\Models\Tool;
use App\Models\User;

class ToolsAdminController extends Controller
{
    private $toolRepository;

    public function __construct(ToolsRepository $toolRepository)
    {
        $this->toolRepository = $toolRepository;
    }

    public function index(Request $request){
        $data['search'] = (object) $request->all();
        $data['tools']  = $this->toolRepository->get($request);
        $data['categories'] = ToolCategory::select('id','category_name')->orderBy('category_name')->get();
        return view('admin.tools.index',$data);
    }

  
    public function store(Request $request){
        $validated = $request->validate([
            'tool_name' => 'required|string|max:200',
            'tool_category_id' => 'required|integer',
            'tool_desc' => 'nullable|string',
            'tool_url' => 'nullable|string'
        ]);

        $tool = ($request->id) ? Tool::find($request->id) : new Tool();
        if(!$tool){
            return back()->with(['message'=>'Tool not found','status'=>'failure']);
        }
        $tool->tool_name = clean_unicode($validated['tool_name'] ?? '');
        $tool->tool_category_id = $validated['tool_category_id'];
        $tool->tool_desc = clean_unicode($validated['tool_desc'] ?? null);
        $tool->tool_url = clean_unicode($validated['tool_url'] ?? null);
        $saved = $request->id ? $tool->update() : $tool->save();

        $data = $saved ? ['message'=>'Tool saved successfully','status'=>'success','data'=>$tool] : ['message'=>'Operation failed, try again','status'=>'failure'];
        return back()->with($data);
    }


    public function destroy(Request $request){
        if(!$request->id){ return back(); }
        $tool = Tool::find($request->id);
        if($tool){ $tool->delete(); }
        return back();
    }

  
}
