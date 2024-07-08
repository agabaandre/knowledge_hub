<?php
namespace App\Repositories;

use App\Models\AccessLog;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MetricsRepository{

    public function country_access(){
        $records = AccessLog::groupBy('country')->select("country",DB::raw("count(id) as count"))->get();
        return [
            'labels'=>$records->pluck("country")->toArray(),
            'values'=>$records->pluck("count")->toArray(),
            "chartType"=> "line"
        ];
    }

    public function country_signups(){
        $records = User::groupBy('country_id')->select("country_id","country.name",DB::raw("count(users.id) as count"))
        ->join('country','country.id',"=",'users.country_id')
        ->where('country.national','National')->get();

        return [
            'labels'=>$records->pluck("name")->toArray(),
            'values'=>$records->pluck("count")->toArray(),
            "chartType"=> "pie"
        ];

    }


    public function monthly_signups(){
            $records = User::groupBy(DB::raw("CONCAT(MONTHNAME(users.created_at),',',YEAR(users.created_at))"))->select(DB::raw("CONCAT(MONTHNAME(users.created_at),',',YEAR(users.created_at)) as month"),DB::raw("count(users.id) as count"))->get();
           
            return [
                'labels'=>$records->pluck("month")->toArray(),
                'values'=>$records->pluck("count")->toArray(),
                "chartType"=> "pie"
            ];

    }

    public function monthly_publications(){
        $records = Publication::groupBy(DB::raw("CONCAT(MONTHNAME(publication.created_at),',',YEAR(publication.created_at))"))->select(DB::raw("CONCAT(MONTHNAME(publication.created_at),',',YEAR(publication.created_at)) as month"),DB::raw("count(publication.id) as count"))->get();
       
        return [
            'labels'=>$records->pluck("month")->toArray(),
            'values'=>$records->pluck("count")->toArray(),
            "chartType"=> "bar"
        ];

}
   

}