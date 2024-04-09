<?php
namespace App\Repositories;
;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Http\Request;

class ToolsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = Tool::orderBy('id','desc');

        if($request->term):
            $query->where('tool_name','like','%'.$request->term.'%');
            $query->orWhere('tool_desc','like','%'.$request->term.'%');
            $query->orWhereIn('tool_category_id',
            ToolCategory::where('category_name','like','%'.$request->term.'%')->pluck('id'));
        endif;

        $results = $query->paginate($rows_count);
        return $results;
    }


}