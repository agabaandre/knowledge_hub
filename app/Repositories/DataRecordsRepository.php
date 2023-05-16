<?php
namespace App\Repositories;

use App\Models\AssetType;
use App\Models\DataCategory;
use App\Models\DataRecord;
use App\Models\DataSubCategory;
use App\Models\HealthAsset;
use Illuminate\Http\Request;

class DataRecordsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataRecord::orderBy('id','desc');

       
        if($request->slug){

          $category       = DataCategory::where('slug','like',$request->slug)->first();
          $sub_categories = DataSubCategory::where('data_category_id',$category->id)
                                            ->get()
                                            ->pluck('id');

          $results->whereIn('data_sub_category_id',$sub_categories);

        }

        if($request->term){

         $results->where('title','like','%'.$request->term.'%');
         $results->orWhere('description','like','%'.$request->term.'%');

        }
      
        if($request->export == 1){
            $this->excel_export($results);
            return;
        }

        return $results->paginate($rows_count);
    }

    
    private function excel_export($results){

        $export_file = 'data-records-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "Title"   => $row->title
                ,"Catgeory"   => $row->sub_category->category->category_name
                ,"Description"   => $row->description
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
        $asset = new DataRecord();
        return $asset;
    }

    public function find($id){

        return DataRecord::find($id);
    }


}