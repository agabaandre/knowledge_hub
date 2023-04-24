<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Favourite;
use App\Models\Publication;
use App\Models\PublicationAttachment;
use App\Models\PublicationSummary;
use App\Models\PublicationTag;
use App\Models\PublicationType;
use App\Models\SubjectArea;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicationsRepository{


    public function get(Request $request,$return_array=false){

        $rows_count = ($request->rows)?$request->rows:20;
        $pubs       = Publication::orderBy('id','desc');

        if($request->term){
            $pubs->where('title','like',$request->term.'%');
            $pubs->orWhere('publication','like',$request->term.'%');
        }

        if($request->author)
         $pubs->where('author_id',$request->author);

         if($request->file_type)
         $pubs->where('file_type_id',$request->file_type);

         if($request->subtheme)
         $pubs->where('sub_thematic_area_id',$request->subtheme);

         if($request->area)
         $pubs->where('geographical_coverage_id',$request->area);

        $results = ($return_array)?$pubs->get():$pubs->paginate($rows_count);

        return  $results->appends($request->all());
    }

    public function with_pending_comments($request){
        
        $rows_count = ($request->rows)?$request->rows:20;

        $pubs = Publication::orderBy('id','desc');
        $pubs = $pubs->whereHas('comments', function($q){
            $q->where('status', 'pending');
        });
        
        $results = $pubs->paginate($rows_count);
        return $results;
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

    public function find_shortened($id){
        return PublicationSummary::find($id);
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

        $pub->author_id            = ($request->author)?$request->author:current_user()->author_id;
        $pub->publication          = $request->link;
        $pub->description          = $request->description;
        $pub->file_type_id         = $request->file_type;
        $pub->visits                   = 0;

        if($request->is_active)
        $pub->is_active = $request->is_active;

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

        $id = ($request->id)?$request->id:$pub->id;

        //save attachments
        if($request->hasFile('files')):
            $files = $request->file('files');
            $this->save_attachments($files,$id);
        endif;

        //save tags
        if($request->tags):
            $this->save_tags($request->tags,$id);
        endif;

        return $pub;
    }

    public function find($id){

        return Publication::find($id);
    }


    public function delete($id){
        return Publication::find($id)->delete();
    }


    public function get_tags(){
        return Tag::all();
    }

    public function save_tags($tags,$publication_id){

        for($i=0;$i<count($tags);$i++){

             $pub_tag = new PublicationTag();
             $pub_tag->tag_id = $tags[$i];
             $pub_tag->publication_id = $publication_id;
             $pub_tag->save();
        }

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
        $fav->delete();

    }

    private function save_attachments($files,$publication_id=null){

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        
        foreach ($upfiles as $file) {

            $description = $file->getClientOriginalName();
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
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

    public function save_summary(Request $request){

        $summary = new PublicationSummary();
        $summary->resource_id = $request->original_id;
        $summary->title       = $request->title;
        $summary->description = $request->summary;

        if($request->hasFile('file')):

            //upload summary
            $file        = $request->file('file');
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
            $file->move(storage_path().'/uploads/publications/summaries/',$file_path);
            $summary->file_path  = $file_path;

        endif;

        $summary->save();
   }



}