<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Author;
use App\Models\Country;
use App\Models\Favourite;
use App\Models\GeoCoverage;
use App\Models\Publication;
use App\Models\PublicationAccessGroup;
use App\Models\PublicationAttachment;
use App\Models\PublicationComment;
use App\Models\PublicationCommunityOfPractice;
use App\Models\PublicationSummary;
use App\Models\PublicationTag;
use App\Models\PublicationType;
use App\Models\Region;
use App\Models\SubjectArea;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\CommunityOfPracticeMembers;
use App\Models\User;
use Illuminate\Http\Request;

class PublicationsRepository extends SharedRepo{


    public function get(Request $request,$return_array=false){

        $rows_count = ($request->rows)?$request->rows:20;
        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])
            ->orderBy('id','desc')->where('is_version',0);

        if($request->order_by_visits):
            $pubs->orderBy('visits','desc');
        else:
            $pubs->orderBy('id','desc');
        endif;

        if($request->is_featured)
            $pubs->where('is_featured',1);

         //search by keyword
        if($request->term){
            
            $pubs->where('title','like',$request->term.'%');
            $pubs->orWhere('publication','like',$request->term.'%');
            
            $authors     = Author::where('name','like',$request->term.'%')->get()->pluck('id');
            $tags        = Tag::where('tag_text','like',$request->term.'%')->get()->pluck('id');
            $pub_tag_ids = PublicationTag::whereIn('tag_id',$tags)->get()->pluck('publication_id');

            if(states_enabled()):
                $coverage    = GeoCoverage::where('name','like','%'.$request->term.'%')->get()->pluck('id');
                $pubs->orWhereIn('geographical_coverage_id',$coverage);
            endif;

            $pubs->orWhereIn('id',$pub_tag_ids);
            $pubs->orWhereIn('author_id',$authors);
        }

        //search administrative unit authors
        if($request->admin_unit){
          
            $authors     = User::where('administrative_unit_id',$request->admin_unit)->get()->pluck('author_id');
            $pubs->whereIn('author_id',$authors);
        }

        //search by author
        if($request->author)
         $pubs->where('author_id',$request->author);

         //search by file type
         if($request->file_type || $request->file_type_id):
            $file_type = ($request->file_type)?$request->file_type:$request->file_type_id;
            $pubs->where('file_type_id',$file_type);
         endif;

        //search by country
         if($request->area)
         $pubs->where('geographical_coverage_id',$request->area);

         //search by tag
         if($request->tag){

            $tag = Tag::where('tag_text',$request->tag)->first();
            $tags = PublicationTag::where('tag_id',$tag->id)->get()->pluck('publication_id');
            $pubs->whereIn('id',$tags->toArray());
        }

        //search by rcc
        if($request->rcc && states_enabled()){

            $rcc = Region::where('id',$request->rcc)->first();
            $country_ids = Country::where('region_id',$rcc->id)->get()->pluck('id');
            $pubs->whereIn('geographical_coverage_id',$country_ids);
        }

         //search by country
        if($request->country_id)
        $pubs->where('geographical_coverage_id',$request->country_id);

         //search by theme
        if($request->thematic_area_id){

            $subthems = SubThemeticArea::where('thematic_area_id',$request->thematic_area_id)->get()->pluck('id');
            $pubs->whereIn('sub_thematic_area_id',$subthems);

        }

         //search by subtheme
        if($request->subtheme || $request->sub_thematic_area_id):
            $subtheme = ($request->subtheme)?$request->subtheme:$request->sub_thematic_area_id;
            $pubs->where('sub_thematic_area_id',$subtheme);
         endif;

         if(current_user() && current_user()->id){

            //Protect Resources from non target audiences if targte audience was defined

            $communties = CommunityOfPracticeMembers::where("user_id",current_user()->id)
            ->pluck("community_of_practice_id");
           
            //forums for user communities
            $commPubs = PublicationCommunityOfPractice::whereIn("community_of_practice_id",$communties)->pluck('publication_id');

            $pubs->whereIn('id',$commPubs)
            ->orWhereDoesntHave("communities");

        }else
        {
            //only those without targets
            $pubs->whereDoesntHave("communities");
        }

         //Access levels effect to query results
        $this->access_filter($pubs);
        
        if(!$request->is_admin){
         $pubs->where('is_active','Active');
         $pubs->where('is_approved',1);
        }


        $results = ($return_array)?$pubs->get():$pubs->paginate($rows_count);

        return   ($return_array)?$results:$results->appends($request->all());
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

        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])->orderBy('id','desc');
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
    
        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])->orderBy('id','desc');
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

        $pub  = ($request->id)? Publication::find($request->id):new Publication();
        $user = ($request->user_id)?User::find($request->user_id):@current_user();
  
        if($request->original_id):

            $parent = $this->find($request->original_id);
            $pub->sub_thematic_area_id     = $parent->sub_thematic_area_id;
            $pub->geographical_coverage_id = $parent->geographical_coverage_id;
            $pub->is_version = 1;
            $pub->title                    = $parent->title;
            $versions_now = count($parent->versioning);
            $pub->version_no  = ($request->version)?$request->version:(($versions_now ==0)?$versions_now +2: $versions_now+1);
            $request['category_id']= $parent->publication_catgory_id;
        else:
           
            $pub->sub_thematic_area_id      = $request->sub_theme;

            if(!$request->geo_area_id):

                $geo_id = ($user->country_id)?$user->area->id:1;
                $pub->geographical_coverage_id = $geo_id;

            else:
                $pub->geographical_coverage_id  = $request->geo_area_id;
            endif;
            
            $pub->title                     = $request->title;

        endif;
        
        $pub->user_id              = $user->id;
        $pub->author_id            = ($request->author)?$request->author: $user->author_id;
        $pub->publication          = $request->link;
        $pub->description          = $request->description;
        $pub->file_type_id         = $request->file_type;
        $pub->publication_catgory_id  = $request->category_id;
        $pub->visits               = ($request->id)?$pub->visits:0;

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
        else:
            if(!$request->id)
             $pub->cover     =  "cover.jpg";
        endif;

        $saved = ($request->id)?$pub->update():$pub->save();

        $id = ($request->id)?$request->id:$pub->id;

        //save attachments
        if($request->hasFile('files') && $saved):
            $files = $request->file('files');
            $this->save_attachments($files,$id);
        endif;

        //save tags
        if($request->tags && $saved):
            $this->save_tags($request->tags,$id);
        endif;

        //attach communitites
        if(@$request->communities && $saved):
            $this->attach_to_community($request->communities,$id);
        endif;

         //attach access groups
         if(@$request->accessgroups && $saved):
            $this->attach_to_access_group($request->accessgroups,$id);
        endif;

        return $pub;
    }

    public function find($id){

        $pub = Publication::with([
            'file_type',
            'attachments',
            'author','sub_theme',
            'comments','parent',
            'summaries','versioning'])->find($id);

        $viewed = get_cookie("Viewed".$pub->id,'yes');

        if(!$viewed):
            $pub->visits = $pub->visits + 1;
            $pub->update();
            set_cookie("Viewed".$pub->id,'yes');
        endif;

       // dd($pub->tag_ids);

        return $pub;
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

    public function attach_to_community($comunities,$publication_id){

        for($i=0;$i<count($comunities);$i++){

             $pub_tag = new PublicationCommunityOfPractice();
             $pub_tag->community_of_practice_id= $comunities[$i];
             $pub_tag->publication_id = $publication_id;
             $pub_tag->save();
        }

    }


    public function attach_to_access_group($groups,$publication_id){

        for($i=0;$i<count($groups);$i++){

             $pub_tag = new PublicationAccessGroup();
             $pub_tag->user_access_group_id= $groups[$i];
             $pub_tag->publication_id = $publication_id;
             $pub_tag->save();
        }

    }


    public function get_types(){
        return PublicationType::all();
    }

    public function get_themes(){
        return SubjectArea::without('kpis')->get();
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
           
            $file->move(storage_path().'/app/public/uploads/publications/',$file_path);

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

       return $file_path;
    }

    public function save_summary(Request $request){

        $summary = new PublicationSummary();
        $summary->resource_id = $request->original_id;
        $summary->title       = $request->title;
        $summary->description = $request->summary;

        $user = @current_user();
       
        if(!$user):
            $user = User::find($request->user_id);
        endif;

        $summary->author_id = $user->author_id;

        if($request->hasFile('file')):

            //upload summary
            $file        = $request->file('file');
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
            $file->move(storage_path().'/uploads/publications/summaries/',$file_path);
            $summary->file_path  = $file_path;

        endif;

       return $summary->save();
   }

   public function  save_comment(Request $request){

    $comment = new PublicationComment();

    $comment->user_id = current_user()->id;
    $comment->publication_id = $request->publication_id;
    $comment->comment = $request->comment;
    $comment->save();

    return $comment;
}

public function change_approval_status(Request $request){

    $publication = Publication::find($request->id);
    
    if($request->approved){

     $publication->is_approved= 1;
     $publication->is_rejected= 0;
     $publication->is_active= 'Active';

     $msg = 'We are happy to inform you that your publication has been approved';
     $action = "Approved";

    }
    else if($request->rejected){

     $publication->is_rejected= 1;
     $publication->is_approved= 0;
     $publication->is_active= 'In-Active';
     $action = "Rejected";

     $msg = 'We are sorry to inform you that your publication has been rejected';

    }

    $publication->update();
    
    $alert = array(
        'title' => "Resource  $publication->title has been $action",
        'body'=>$msg,
        'email'=>@$publication->user->email
    );
    SendMailJob::dispatch( $alert);

    return $publication;
}

public function approve_comment($id){

    $comment = PublicationComment::find($id);
    $comment->status = 'approved';
    $comment->update();

    $alert = array(
        'title' => "Comment  $comment->comment has been Approved",
        'body'=>'We are happy to inform you that your comment has been approved',
        'email'=>$comment->user->email
    );

    SendMailJob::dispatch( $alert);

}

public function reject_comment($id){

    $comment = PublicationComment::find($id);
    $comment->status='rejected';
    $comment->update();

    $alert = array(
        'title' => "Comment  $comment->comment has been Rejected",
        'body'=>'We are sorry to inform you that your comment has been rejected',
        'email'=>$comment->user->email
    );

    SendMailJob::dispatch( $alert);

}

public function get_summaries($request){
    $qry =  PublicationSummary::orderBy('id','desc');
    return $qry->paginate(15);
}

public function find_summary(Request $request){
    return PublicationSummary::find($request->id);
}

public function sumamry_approval_status(Request $request){

    $record = PublicationSummary::find($request->id);
    
    if($request->approved){

     $record->is_approved= 1;
     $record->is_rejected= 0;

     $msg = 'We are happy to inform you that your sumamry/abstract has been approved';
     $action = "Approved";

    }
    
    else if($request->rejected){

     $record->is_rejected= 1;
     $record->is_approved= 0;
     $action = "Rejected";

     $msg = 'We are sorry to inform you that your sumamry/abstract has been rejected';

    }

    $record->update();
    
    $alert = array(
        'title' => "Resource  $record->title has been $action",
        'body'=>$msg,
        'email'=>@$record->user->email
    );
    SendMailJob::dispatch( $alert);

    return $record;
}




}