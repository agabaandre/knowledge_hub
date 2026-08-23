<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeUnit;
use App\Repositories\AdminUnitsRepository;
use App\Repositories\PublicationsRepository;
use Illuminate\Http\Request;

class AdminUnitFrontEndController extends Controller
{
    private $adminUnitsRepository,$publicationsRepository;

    public function __construct(AdminUnitsRepository $adminUnitsRepository,PublicationsRepository $publicationsRepository)
    {
        $this->adminUnitsRepository    = $adminUnitsRepository;
        $this->publicationsRepository = $publicationsRepository;
    }
    
    public function index(Request $request){

        $request->merge(['rows' => 'all']);
        $data['adminunits'] = $this->adminUnitsRepository->get($request);
        $data['show_admin_units_map'] = admin_units_map_enabled();
        $data['admin_units_map_settings'] = admin_units_map_settings_for_js();

        return view('adminunits.index',$data);
    }

    public function mapData(Request $request)
    {
        if (! admin_units_map_enabled()) {
            return response()->json(['points' => [], 'unit_count' => 0], 403);
        }

        return response()->json($this->adminUnitsRepository->get_map_values());
    }


	public function show(Request $request){

        $data['unit']          = $this->adminUnitsRepository->find($request->id);
        $data['child_units']   = $this->adminUnitsRepository->child_units($request->id);
       
        $request['admin_unit'] = $request->id;
        $request['rows']       = 10;
		$data['publications']  = $this->publicationsRepository->get($request);
       
        return view('adminunits.details',$data);
    }



}
