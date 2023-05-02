<?php
namespace App\Repositories;

use App\Models\AssetType;
use App\Models\HealthAsset;
use Illuminate\Http\Request;

class AssetsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = HealthAsset::orderBy('id','desc');

       
        if($request->slug){
          $type = AssetType::where('slug','like','%'.$request->slug.'%')->first();
          
          if($type)
            $results->where('asset_type_id',$type->id);
        }

        if($request->term)
        $results->where('asset_type_id','like','%'.$request->term.'%');
      
        if($request->export == 1){
            $this->excel_export($results);
            return;
        }

        return $results->paginate($rows_count);
    }

    
    private function excel_export($results){

        $export_file = 'assets-list-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "Asset Name"   => $row->asset_name
                ,"Type"   => $row->type->name
                ,"Description"   => $row->asset_desc
                ,"Url"=>$row->url
                ,"Country"  =>($row->country)?$row->country->name:''
               ];

               array_push($export_data,$data_row);
            }

        });

       set_time_limit(0);

        $filename =  $export_file;      
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

       export_excel($export_data);
    }
    
    public function save(Request $request){
        $asset = new HealthAsset();
        return $asset;
    }

    public function find($id){

        return HealthAsset::find($id);
    }


}