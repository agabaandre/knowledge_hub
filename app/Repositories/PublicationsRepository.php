<?php
namespace App\Repositories;

use App\Models\Publication;
use App\Models\PublicationTag;
use App\Models\PublicationType;
use App\Models\SubjectArea;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicationsRepository{


    public function get(Request $request,$return_array=false){

        $rows_count = ($request->rows)?$request->rows:20;
        $pubs = Publication::orderBy('id','desc');

        if($request->term){
            $pubs->where('title','like',$request->term.'%');
            $pubs->orWhere('publication','like',$request->term.'%');
        }

        if($request->author)
         $pubs->where('author_id',$request->author);

         if($request->subtheme)
         $pubs->where('sub_thematic_area_id',$request->subtheme);

        
        $results = ($return_array)?$pubs->get():$pubs->paginate($rows_count);

        return  $results;
    }
    
    public function favourites(Request $request){
        $pub = new Publication();

        return $pub;
    }


    public function save(Request $request){
        $pub = new Publication();

        return $pub;
    }

    public function find($id){

        return Publication::find($id);
    }

    public function get_tags(){
        return Tag::all();
    }

    public function get_types(){
        return PublicationType::all();
    }

    public function get_themes(){
        return SubjectArea::all();
    }

    public function get_subthemes(){
        return SubThemeticArea::all();
    }

    public function get_subtheme($id){
        return SubThemeticArea::find($id);
    }





}