<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;

class HealthThemesController extends Controller
{
    private $themesRepo;

    public function __construct(ThemesRepository $themesRepo)
    {
        $this->themesRepo = $themesRepo;
    }

    public function index(Request $request){

        $data['themes'] = $this->themesRepo->get($request);
        $data['allThemesForMapping'] = $this->themesRepo->allForMapping();
        $data['faIconOptions'] = $this->themesRepo->fontAwesomeIconOptions();
        $data['faVersion'] = $this->themesRepo->fontAwesomeVersion();
        $data['faCheatsheetUrl'] = $this->themesRepo->fontAwesomeCheatsheetUrl();
        $data['search'] = (Object) $request->all();
        return view('admin.themes.index',$data);
    }

    public function store(Request $request){
        $request->validate([
            'display_order' => 'nullable|integer|min:0',
        ]);

        $saved = $this->themesRepo->save($request);

        if($saved):
            $data = ['message'=>'Subject Area saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }


    public function destroy(Request $request){
        if (!auth()->user() || !auth()->user()->can('delete_meta_data')) {
            return response(['status'=>'failure','message'=>'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:thematic_area,id',
            'replacement_theme_id' => 'required|integer|exists:thematic_area,id|different:id',
        ]);

        $result = $this->themesRepo->deleteWithMapping((int) $request->id, (int) $request->replacement_theme_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }


}
