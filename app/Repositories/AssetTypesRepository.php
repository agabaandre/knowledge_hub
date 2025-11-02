<?php
namespace App\Repositories;

use App\Models\AssetType;
use Illuminate\Http\Request;

class AssetTypesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows) ? $request->rows : 20;
        $query = AssetType::orderBy('id', 'desc');

        if($request->term) {
            $query->where('type_name', 'like', '%' . $request->term . '%');
            $query->orWhere('type_desc', 'like', '%' . $request->term . '%');
        }

        return $query->paginate($rows_count)->appends($request->all());
    }

    public function save(Request $request){

        $asset_type = ($request->id) ? AssetType::find($request->id) : new AssetType();
        
        $asset_type->type_name = $request->type_name;
        
        if(isset($request->type_desc)) {
            $asset_type->type_desc = $request->type_desc;
        }

        return ($request->id) ? $asset_type->update() : $asset_type->save();
    }

    public function find($id){
        return AssetType::find($id);
    }

    public function delete($id){
        return AssetType::find($id)->delete();
    }
}

