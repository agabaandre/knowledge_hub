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

        $data['adminunits'] = $this->adminUnitsRepository->get($request);
        return view('adminunits.index',$data);
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
