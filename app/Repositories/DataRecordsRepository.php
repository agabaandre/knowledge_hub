<?php
namespace App\Repositories;

use App\Models\AssetType;
use App\Models\Country;
use App\Models\DataCategory;
use App\Models\DataRecord;
use App\Models\DataSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataRecordsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataRecord::orderBy('id','desc');

       
        if($request->slug){
          $category       = DataCategory::where('slug',$request->slug)->first();
          $results->where('data_category_id',$category->id);
        }

        if($request->term){

         $results->where('title','like','%'.$request->term.'%');
         $results->orWhere('description','like','%'.$request->term.'%');
        }

        if($request->rcc)
            $results->whereIn('country_id',Country::where('region_id',$request->rcc)->pluck('id'));
        
        if($request->country_id)
            $results->where('country_id',$request->country_id);
      
        if($request->export == 1){
            $this->excel_export($results);
            return;
        }

        $this->access_filter($results);

        return $results->paginate($rows_count);
    }

    
    private function excel_export($results){

        $export_file = 'data-records-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "Title"   => $row->title
                ,"Category"   => $row->sub_category->category->category_name ?? ''
                ,"Description"   => $row->description
                //,"Url"=>$row->url
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
        $record = new DataRecord();

        $record->title       = $request->title;
        $record->description = $request->description;
        $record->url = $request->link;
        $record->data_category_id =$request->data_category_id;;
        $record->data_sub_category_id = $request->data_sub_category_id;
        $record->country_id   = $request->country_id;
        $record->file_type_id = $request->file_type_id;
        $record->is_embedded  = (@$request->embedded)?1:0;

        return $record->save();
    }

    public function find($id){

        return DataRecord::find($id);
    }

    public function delete($id){
        return DataRecord::find($id)->delete();
    }

    public function get_categories(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataCategory::orderBy('id','desc');

        return $results->paginate($rows_count);
    }

    public function get_subcategories(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataSubCategory::orderBy('id','desc');

        return $results->paginate($rows_count);
    }

    public function get_json_countries() {
        $results = Country::all();
        return $results;
    }

    public function get_json_categories()
    {
        $results = DataCategory::all();
        return $results;
    }

    public function delete_category($id){

        return DataCategory::find($id)->delete();
    }


    public function save_category(Request $request){
        $record = new DataCategory();

        $record->category_name    = $request->name;
        $record->url_path         = $request->url;
        $record->slug             = Str::slug($request->title);
        $record->show_on_menu     = $request->show_menu;

        return $record->save();
    }

    public function save_subcategory(Request $request){

        $record = new DataSubCategory();

        $record->sub_catgeory_name   = $request->name;
        $record->data_category_id         = $request->category_id;

        return $record->save();
    }

}