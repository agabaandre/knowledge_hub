<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use Illuminate\Http\Request;

class ThemesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $themes = ThemeticArea::paginate($rows_count);

        return $themes;
    }

    public function get_subthemes(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $themes = SubThemeticArea::where('thematic_area_id',$request->id)->paginate($rows_count);

        return $themes;
    }
    
    public function save(Request $request){
        $quote = new ThemeticArea();

        return $quote;
    }

    public function find($id){

        return ThemeticArea::find($id);
    }


}