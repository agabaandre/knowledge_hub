<?php

namespace App\Http\Controllers\Admin;

use App\Models\ThemeticArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;

class SubHealthThemesController extends Controller
{
    private $themesRepo;

    public function __construct(ThemesRepository $themesRepo)
    {
        $this->themesRepo = $themesRepo;
    }

    public function index(Request $request){

        $data['themes'] = ThemeticArea::query()
            ->orderBy('display_order', 'asc')
            ->orderBy('description', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $request->merge(['datatable' => true]);
        $data['subthemes'] = $this->themesRepo->get_all_subthemes($request);
        $data['allSubthemesForMapping'] = $this->themesRepo->allSubthemesForMapping();
        $data['faIconOptions'] = $this->themesRepo->fontAwesomeIconOptions();
        $data['faVersion'] = $this->themesRepo->fontAwesomeVersion();
        $data['faCheatsheetUrl'] = $this->themesRepo->fontAwesomeCheatsheetUrl();
        $data['selectedThemeId'] = (int) ($request->input('theme_id', $request->input('thematic_area_id', 0)));
        $data['search'] = (Object) $request->all();

        return view('admin.subthemes.index',$data);
    }

    public function store(Request $request){
        $request->validate([
            'detailed_description' => 'nullable|string|max:10000',
        ]);

        $saved = $this->themesRepo->save_subtheme($request);

        if($saved):
            $data = ['message'=>'Subtheme saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }


    public function destroy(Request $request){
        if (!auth()->user() || !auth()->user()->can('delete_publication_metadata')) {
            return response(['status'=>'failure','message'=>'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:sub_thematic_area,id',
            'replacement_subtheme_id' => 'required|integer|exists:sub_thematic_area,id|different:id',
        ]);

        $result = $this->themesRepo->deleteSubthemeWithMapping((int) $request->id, (int) $request->replacement_subtheme_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }


}
