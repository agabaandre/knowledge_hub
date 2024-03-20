<?php
namespace App\Repositories;

use App\Models\Country;
use App\Models\Expert;
use App\Models\ExpertType;
use Illuminate\Http\Request;

class ExpertsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = Expert::orderBy('id','desc');

        if($request->term):
            $query->where('first_name','like','%'.$request->term.'%');
            $query->orWhere('last_name','like','%'.$request->term.'%');
            $query->orWhere('job_title','like','%'.$request->term.'%');
            
            $query->orWhereIn('expert_type_id',
            ExpertType::where('type_name','like','%'.$request->term.'%')->pluck('id'));
            
            $query->orWhereIn('country_id',
            Country::where('name','like','%'.$request->term.'%')->pluck('id'));
        endif;

        if($request->export == 1){
            $this->excel_export($query);
            return;
        }
       
        $this->access_filter($query);

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
                ,"Job"         =>$row->job_title
                ,"Occupation"  =>$row->occupation
                ,"Expert Type" =>$row->expert_type
                ,"Email"       => $row->email
                ,"Telephone"   =>$row->phone_number
                ,"Country"     =>($row->country)?$row->country->name:''
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

        $expert = ($request->id)?Expert::find($request->id):new Expert();

        $expert->first_name = $request->first_name;
        $expert->last_name  = $request->last_name;
        $expert->job_title  = $request->job_title;
        $expert->email      = $request->email;
        $expert->phone_number    = $request->phone_number;
        $expert->expert_type_id  = $request->type_id;
        $expert->country_id      = $request->country_id;

        $saved= ($request->id)?$expert->update():$expert->save();

        return $expert;
    }

    public function find($id){
        return Expert::find($id);
    }

    public function delete($id){

        return Expert::find($id)->delete();
    }

    public function get_types(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = ExpertType::orderBy('id','desc');

        if($request->term):
            $query->where('type_name','like','%'.$request->term.'%');
        endif;
        $results = $query->paginate($rows_count);
        return $results;
    }

    public function save_type(Request $request){

        $expert_type = ($request->id)?ExpertType::find($request->id):new ExpertType();

        $expert_type->type_name = $request->type;
        $expert_type->type_desc  = $request->description;

        return ($request->id)?$expert_type->update():$expert_type->save();
    }

    public function delete_type($id){

        return ExpertType::find($id)->delete();
    }


}