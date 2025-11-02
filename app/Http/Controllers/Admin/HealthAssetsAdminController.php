<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AssetsRepository;

class HealthAssetsAdminController extends Controller
{
    private $assetsRepo;

    public function __construct(AssetsRepository $assetsRepo)
    {
        $this->assetsRepo = $assetsRepo;
    }

    public function index(Request $request){
        $data['assets'] = $this->assetsRepo->get($request);
        $data['search'] = (Object) $request->all();
        
        // Load asset types for filter dropdown
        $data['asset_types'] = \App\Models\AssetType::orderBy('type_name')->get();
        
        return view('admin.healthassets.index', $data);
    }

    public function details(Request $request){
        $data['asset'] = $this->assetsRepo->find($request->id);
        return view('admin.healthassets.details', $data);
    }
}

