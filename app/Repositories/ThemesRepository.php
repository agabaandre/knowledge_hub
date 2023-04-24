<?php
namespace App\Repositories;

use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use Illuminate\Http\Request;

class ThemesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;

        $themes = ThemeticArea::orderBy('id','desc');

        if($request->term)
        $themes->where('description','like','%'.$request->term.'%');
        $result = $themes->paginate($rows_count);

        return $result;
    }

    public function get_subthemes(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $themes = SubThemeticArea::where('thematic_area_id',$request->id)->paginate($rows_count);

        return $themes;
    }

    public function get_all_subthemes(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $themes = SubThemeticArea::orderBy('id','desc');
        if($request->term)
        $themes->where('description','like','%'.$request->term.'%');
        $result = $themes->paginate($rows_count);

        return $result;
    }
    
    public function save(Request $request){
        $theme = new ThemeticArea();
        $theme->description = $request->name;
        $theme->icon = $request->icon;
        $theme->save();

        return $theme;
    }

    public function find($id){

        return ThemeticArea::find($id);
    }

    public function delete($id){
        return ThemeticArea::find($id)->delete();
    }

    public function save_subtheme(Request $request){
        $theme = new SubThemeticArea();
        $theme->description = $request->name;
        $theme->icon = $request->icon;
        $theme->save();

        return $theme;
    }

    public function delete_subtheme($id){
        return SubThemeticArea::find($id)->delete();
    }



}