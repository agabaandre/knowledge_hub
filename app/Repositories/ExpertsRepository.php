<?php
namespace App\Repositories;

use App\Models\Expert;
use Illuminate\Http\Request;

class ExpertsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = Expert::orderBy('id','desc');

        if($request->term):
            $query->where('first_name','like','%'.$request->term.'%');
            $query->orWhere('last_name','like','%'.$request->term.'%');
            $query->orWhere('job_title','like','%'.$request->term.'%');
            $query->orWhere('expert_type','like','%'.$request->term.'%');
        endif;

        if($request->export == 1){
            $this->excel_export($query);
            return;
        }
       
        $results = $query->paginate($rows_count);
        return $results;
    }

    private function excel_export($results){

        $export_file = 'experts-list-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "First Name"   => $row->first_name
                ,"Last Name"   => $row->last_name
                ,"Job"=>$row->job_title
                ,"Occupation"=>$row->occupation
                ,"Expert Type"=>$row->expert_type
                ,"Email"    => $row->email
                ,"Telephone"=>$row->phone_number
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
        $asset = new Expert();
        return $asset;
    }

    public function find($id){

        return Expert::find($id);
    }


}