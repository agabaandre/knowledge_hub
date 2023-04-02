<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Favourite;
use App\Models\Publication;
use App\Models\PublicationAttachment;
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

    public function my_publications(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $author_id  = current_user()->author_id;

        $pubs = Publication::orderBy('id','desc');
        $pubs->where('author_id',$author_id);
        $result = $pubs->paginate($rows_count);

        return $result;
    }
    
    public function favourites(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $user_id    =  current_user()->id;
        
        $favs = Favourite::where('user_id',$user_id)
        ->get()
        ->pluck('publication_id')
        ->toArray();
    
        $pubs = Publication::orderBy('id','desc');
        $pubs->whereIn('id',$favs);

        $result = $pubs->paginate($rows_count);

        return $result;
    }

    public function find_type($id){
        return PublicationType::find($id);
    }

    public function save(Request $request){

        $pub = new Publication();

        if($request->original_id):

            $parent = $this->find($request->original_id);
            $pub->sub_thematic_area_id     = $parent->sub_thematic_area_id;
            $pub->geographical_coverage_id = $parent->geographical_coverage_id;
            $pub->is_version = 1;
            $pub->title                    = $parent->title;
            $versions_now = count($parent->versioning);
            $pub->version_no               = ($versions_now ==0)?$versions_now +2: $versions_now+1;

        else:
           
        $pub->sub_thematic_area_id      = $request->sub_theme;
        $pub->geographical_coverage_id  = $request->geo_area_id;
        $pub->title                     = $request->title;

        endif;

        $pub->author_id            = current_user()->author_id;
        $pub->publication          = $request->link;
        $pub->description          = $request->description;
        $pub->file_type_id         = $request->file_type;
        $pub->visits                   = 0;

        $file_type = $this->find_type($request->file_type);

        //check if it's video type
        if(strpos(strtolower($file_type->name),'video')>-1)
         $pub->is_video = 1;
      
        //save cover
        if($request->hasFile('cover')):
            $file           = $request->file('cover');
            $cover_filepath = $this->save_attachments($file);
            $pub->cover     = $cover_filepath;
        endif;

        $saved = ($request->id)?$pub->update():$pub->save();

        //save attachments
        if($request->hasFile('files')):
            $files = $request->file('files');
            $this->save_attachments($files,$pub->id);
        endif;

       
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

    public function add_favourite($pub_id){

         $fav = new Favourite();
         $fav->user_id = current_user()->id;
         $fav->publication_id = $pub_id;
         $fav->save();
    }

    public function remove_favourite($pub_id){

        $fav = Favourite::where('publication_id',$pub_id)
        ->where('user_id',current_user()->id)
        ->first();

        if($fav)
        $fav->destroy();

    }

    private function save_attachments($files,$publication_id=null){

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        
        foreach ($upfiles as $file) {

            $description = $file->getClientOriginalName();
            $file_name = md5_file($file->getRealPath());
            $extension = $file->guessExtension();
            $file_path = $file_name.'.'.$extension;
           
            $file->move(storage_path().'/uploads/publications/',$file_path);

    
        //insert if to be in different table
        if($publication_id):

            $kyc   =  [
            "description"=>$description,
            "file"=> $file_path,
            "publication_id"=>$publication_id
           ];
       
         PublicationAttachment::insert($kyc);

        endif;

       }

       $file_path;
    }





}