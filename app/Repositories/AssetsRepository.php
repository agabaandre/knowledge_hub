<?php
namespace App\Repositories;

use App\Models\HealthAsset;
use Illuminate\Http\Request;

class AssetsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = HealthAsset::orderBy('id','desc');
        
        if($request->slug){
          $results->where('asset_desc','like','%'.$request->slug.'%');
          $results->orWhere('asset_name','like','%'.$request->slug.'%');
        }

        return $results->paginate($rows_count);
    }
    
    public function save(Request $request){
        $asset = new HealthAsset();
        return $asset;
    }

    public function find($id){

        return HealthAsset::find($id);
    }


}