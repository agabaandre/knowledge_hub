<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Kpi;
use App\Models\KpiData;
use Illuminate\Http\Request;

class IndicatorsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $authors = Kpi::orderBy('id','desc');

        if($request->term){
            $authors->where('name','like','%'.$request->term.'%');
            $authors->orWhere('name','like','%'.$request->term.'%');
        }
        

        $result = $authors ->paginate($rows_count);

        return  $result;
    }
    
    public function save(Request $request){

        $kpi = new Kpi();
        $kpi->name         = $request->name;
        $kpi->description  = $request->description;
        $kpi->subject_area = 1; //$request->subject_area_id;
        $kpi->frequency    = $request->frequency;
        $kpi->save();

        return $kpi;
    }

    public function get_data(Request $request){

        $data = KpiData::paginate(20);

        return $data;

    }

    public function find($id){

        return Author::find($id);
    }

    public function delete($id){

        return Author::find($id)->delete();
    }


}