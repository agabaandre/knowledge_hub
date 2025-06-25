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
use App\Models\ContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Imports\PublicationImport;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use DB;

class PublicationsRepository extends SharedRepo{
    
public function get(Request $request, $return_array = false, $featured = false,$pending=false)
{
    $rows_count = $request->rows ?? 20;

    $pubs = Publication::with([
        'file_type', 'author', 'sub_theme', 'category', 'country', 'comments', 'versioning', 'parent'
    ])
    ->where('is_version', 0)
    ->inRandomOrder()
    ->orderBy($request->order_by_visits ? 'visits' : 'id', 'desc')
    ->searchTerm($request->term);

    if ($featured && current_user()) {
        // Optimized: Cache user preferences
        $user = current_user();
        $cacheKey = "user_preferences_{$user->id}";
        $subthemes = cache()->remember($cacheKey, 3600, function() use ($user) {
            return $user->preferences()->pluck('subtheme_id');
        });
        $pubs->featured($subthemes);
    } 
    elseif ($featured) {
        $pubs->where('is_featured', 1);
    }

    if (!$featured) {
        $this->applyFilters($pubs, $request);
    }

    $pubs->when(!is_admin(), function ($query) use ($request) {
        $query->where('is_admin_only_access', 0)
            ->where('is_active', 'Active')
            ->where('is_approved', 1);

        if (auth()->user()) {
            // Optimized: Cache user communities
            $user = auth()->user();
            $cacheKey = "user_communities_{$user->id}";
            $userCommunities = cache()->remember($cacheKey, 1800, function() use ($user) {
                return CommunityOfPracticeMembers::where('user_id', $user->id)
                    ->where('is_approved', 1)
                    ->pluck('community_of_practice_id');
            });

            $query->when(!$request->community_id, function ($query) use ($userCommunities, $user) {
                $query->where(function ($q) use ($userCommunities, $user) {
                    if ($userCommunities->count() > 0) {
                        $q->whereHas('communities', function ($q) use ($userCommunities) {
                            $q->whereIn('community_of_practice_id', $userCommunities);
                        });
                    }
                    $q->orWhereDoesntHave('communities')
                      ->orWhere('user_id', $user->id);
                });
            }, function ($query) use ($request) {
                $query->whereHas('communities', function ($q) use ($request) {
                    $q->where('community_of_practice_id', $request->community_id);
                });
            });
        } 
        else {
            $query->whereDoesntHave('communities');
        }
    }, function ($query) {
        $this->access_filter($query);
    });

    if($pending) {
        $pubs->where('is_approved', 0);
    }

    $results = $pubs->paginate($rows_count)->appends($request->all());

    return $return_array ? $results : $results;
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
        $user_id  = auth()->user()->id;

        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])->orderBy('id','desc');
        $pubs->where('user_id',$user_id);
        $result = $pubs->paginate($rows_count);

        return $result;
    }
    
    public function favourites(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $user_id    =  auth()->user()->id;
        
        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])
            ->whereHas('favourites', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('id', 'desc');

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

        Log::info("Request:: ". json_encode($request->all()));

        $pub  = ($request->id)? Publication::find($request->id):new Publication();
        $user = ($request->user_id)?User::find($request->user_id):auth()->user();
  
        if($request->original_id):

            $parent = $this->find($request->original_id);
            $pub->sub_thematic_area_id     = $parent->sub_thematic_area_id;
            $pub->geographical_coverage_id = $parent->geographical_coverage_id;
            $pub->is_version = 1;
            $pub->title                    = $parent->title;
            $versions_now = count($parent->versioning);
            $pub->version_no  = ($request->version)?$request->version:(($versions_now ==0)?$versions_now +2: $versions_now+1);
            $request['category_id']= $parent->data_category_id;
            $request['data_category_id'] = $parent->publication_catgory_id;
            
        else:
            $pub->sub_thematic_area_id      = $request->sub_theme;

            if(!$request->countries):
                $geo_id = ($user->country_id)?$user->area->id:1;
                $pub->geographical_coverage_id = $geo_id;
            else:
                $pub->geographical_coverage_id  = $request->countries[0];
            endif;
            
            $pub->title                     = $request->title;

        endif;
        
        $pub->user_id              = $user->id;
        $pub->author_id            = ($request->author)?$request->author: $user->author_id;
        $pub->publication          = $request->link;
        $pub->description          = $request->description;
        $pub->publication_catgory_id  = $request->data_category_id;
        $pub->associated_authors     = $request->associated_authors;
        $pub->visits                 = ($request->id)?$pub->visits:0;
        $pub->data_category_id       = $request->category_id;
        $pub->is_embedded            = $request->is_embedded ?? false;
        $pub->is_default_in_category = $request->is_default ?? false;
        $pub->is_admin_only_access   = $request->admin_only ?? false;
        $pub->show_disclaimer        = $request->show_disclaimer ?? false;


        if(!is_admin()){

            $pub->is_active   = 'In-Active';
            $pub->is_approved = 0;
            $pub->is_rejected = 0;

            if($request->author)
            $pub->geographical_coverage_id  = Author::find($request->author)->user->country_id;
        }

        //save cover
        if($request->hasFile('cover')):

            $file           = $request->file('cover');
            $cover_filepath = $this->save_attachments($file);
            $pub->cover     = $cover_filepath;
            $filepath       = $cover_filepath;
        else:
            if(!$request->id)
             $pub->cover     =  "cover.jpg";
        endif;

        $saved = ($request->id)?$pub->update():$pub->save();

        $id = ($request->id)?$request->id:$pub->id;

        $attachment_path =null;
        //save attachments
        if($request->hasFile('files') && $saved):
            $files = $request->file('files');
            $attachment_path = $this->save_attachments($files,$id);
        endif;

        $attachment_path = ($attachment_path)?storage_path('/app/public/uploads/publications/'.$attachment_path):null;
        $file_type = get_file_type($attachment_path,$request->link);

        //check if it's video type
        if(strpos(strtolower($file_type->name),'video')>-1)
         $pub->is_video = 1;
         
        $pub->file_type_id =$file_type->id; //$request->file_type;
        $pub->update();
      
        //attach communitites
        if(@$request->communities && $saved):
            $this->attach_to_community($request->communities,$id);
        endif;

         //attach access groups
         if(@$request->accessgroups && $saved):
            $this->attach_to_access_group($request->accessgroups,$id);
        endif;

        if($saved):
            $this->attach_countries($pub,$request);
        endif;
        
        if(!is_admin()){

            $alert = array(
                'title' => "Resource  $pub->title has been". ($request->id)?' Edited':' Submitted fpr approval',
                'body'=>"Your attention is required to review is called upon",
                'email'=>"adminemail@gmail.com" // put right admin here
            );
            SendMailJob::dispatch( $alert);
        }

        return $pub;
    }

    public function attach_countries($publication,$request){
        
        $rccs = (is_array($request->rccs))?$request->rccs:json_decode($request->rccs);
        $countries = (is_array($request->countries))?$request->countries:json_decode($request->countries);
        $countryIds = [];
        $regionIds  = [];

        if ($rccs[0] == 'all' || (is_array($countries) && strtolower($countries[0]) === 'all')):
            $countryIds = Country::pluck('id')->toArray();
        else:
            if($rccs[0] == 'all')
                $regionIds = Region::pluck('id')->toArray();
            else
                $regionIds = Region::whereIn('id', $rccs)->pluck('id')->toArray();

            if (!empty($regionIds))
                $countryIds = Country::whereIn('region_id', $regionIds)->pluck('id')->toArray();
            else
                $countryIds = $countries;
            
            if(count($countryIds) > 0)
                $publication->countries()->attach($countryIds);
        endif;
    }

    public function find($id){

        $pub = Publication::with([
            'file_type',
            'attachments',
            'author','sub_theme',
            'comments','parent',
            'summaries','versioning',
            'sub_category','data_category'])->find($id);

        $cookie_name = "Viewed".$pub->id.((auth()->user() && auth()->user()->id)?auth()->user()->id :'');
        $viewed      = get_cookie($cookie_name);

        if(!$viewed):
            $pub->visits = $pub->visits + 1;
            $pub->update();
            set_cookie("Viewed".$pub->id,'yes');
        endif;

        return $pub;
    }

    public function delete($id){
        return Publication::find($id)->delete();
    }

    public function get_tags(){
        return Tag::all();
    }

    public function save_tags($tags,$publication_id){

        // Optimized: Use bulk insert instead of individual inserts in a loop
        $tagData = [];
        foreach($tags as $tag_id) {
            $tagData[] = [
                'tag_id' => $tag_id,
                'publication_id' => $publication_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($tagData)) {
            PublicationTag::insert($tagData);
        }
    }

    public function attach_to_community($comunities,$publication_id){

        try{
            
        if(!is_array($comunities))
        $comunities = json_decode($comunities);

        if(is_array($comunities)):
            // Optimized: Use bulk insert instead of individual inserts in a loop
            $communityData = [];
            foreach($comunities as $community_id) {
                $communityData[] = [
                    'community_of_practice_id' => $community_id,
                    'publication_id' => $publication_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            if (!empty($communityData)) {
                PublicationCommunityOfPractice::insert($communityData);
            }
       endif;
    }
    catch(\Exception $exception){
            Log::error("Error occured". $exception->getMessage());
    }

    }


    public function attach_to_access_group($groups,$publication_id){

        // Optimized: Use bulk insert instead of individual inserts in a loop
        $groupData = [];
        foreach($groups as $group_id) {
            $groupData[] = [
                'user_access_group_id' => $group_id,
                'publication_id' => $publication_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($groupData)) {
            PublicationAccessGroup::insert($groupData);
        }
    }


    public function get_types(){
        return PublicationType::all();
    }

    public function get_themes(){
        return SubjectArea::without('kpis')->get();
    }

    public function get_subthemes($request=null){
        $qry = SubThemeticArea::orderBy('description','asc');
        
        if($request && $request->thematic_area_id)
        $qry->where('thematic_area_id',$request->thematic_area_id);

        return $qry->get();
    }

    public function get_subtheme($id){
        return SubThemeticArea::find($id);
    }

    public function add_favourite($pub_id){

         $fav = new Favourite();
         $fav->user_id = auth()->user()->id;
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
        $attachmentData = [];
        
        foreach ($upfiles as $file) {

            $description = $file->getClientOriginalName();
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
            $file->move(storage_path().'/app/public/uploads/publications/',$file_path);

            // Optimized: Collect attachment data for bulk insert
            if($publication_id) {
                $attachmentData[] = [
                    "description" => $description,
                    "file" => $file_path,
                    "publication_id" => $publication_id,
                    "created_at" => now(),
                    "updated_at" => now()
                ];
            }
        }

        // Optimized: Use bulk insert instead of individual inserts
        if (!empty($attachmentData)) {
            PublicationAttachment::insert($attachmentData);
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
            $file->move(storage_path().'/app/public/uploads/publications/summaries/',$file_path);
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

   
    $publication = ($request->is_summary)?PublicationSummary::find($request->id):Publication::find($request->id);
    
    
    if($request->approved){

     $publication->is_approved= 1;
     $publication->is_rejected= 0;

     if(!$request->is_summary)
     $publication->is_active= 'Active';

     $msg = 'We are happy to inform you that your publication has been approved';
     $action = "Approved";

    }
    else if($request->rejected){

     $publication->is_rejected= 1;
     $publication->is_approved= 0;

     if(!$request->is_summary)
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

public function save_content_request(Request $request){

   $record = new ContentRequest();
   $record->subject     = $request->title;
   $record->description = $request->description;
   $record->country_id  = (auth()->user())?auth()->user()->country_id:$request->country_id;

   if($request->email)
   $record->email = $request->email;
   $record->created_at= Carbon::now();
   $record->updated_at= Carbon::now();

   $record->save();
   return $record;
}

public function import(Request $request){

    if($request->hasFile('file')):

        Excel::import( new PublicationImport(), request()->file('file'));
        return true;

    else:

        dd("No file");

    endif;

}

// New method to apply filters
private function applyFilters($query, $request) {

    $filters = [
        'admin_unit' => function ($q, $value) {
            $authors = User::where('administrative_unit_id', $value)->pluck('author_id');
            $q->whereIn('author_id', $authors);
        },
        'author' => function ($q, $value) {
            $q->where('author_id', $value);
        },
        'file_type' => function ($q, $value) {
            $q->where('file_type_id', $value);
        },
        'area' => function ($q, $value) {
            $q->where('geographical_coverage_id', $value)
            ->orWhereHas('countries', function($subQuery) use ($value) {
                $subQuery->where('country.id', $value);
            });
        },
        'rcc' => function ($q, $value) {
            if (states_enabled() && $value !=='all') {
                $country_ids = Country::where('region_id', $value)->pluck('id');
                $q->where(function($query) use ($country_ids) {
                    $query->whereIn('geographical_coverage_id', $country_ids)
                          ->orWhereHas('countries', function($subQuery) use ($country_ids) {
                              $subQuery->whereIn('country.id', $country_ids);
                          });
                });
            }
        },
        'country_id' => function ($q, $value) {
            $q->where('geographical_coverage_id', $value)
            ->orWhereHas('countries', function($subQuery) use ($value) {
                $subQuery->where('country.id', $value);
            });
        },
        'thematic_area_id' => function ($q, $value) {
            $subthems = SubThemeticArea::where('thematic_area_id', $value)->pluck('id');
            $q->whereIn('sub_thematic_area_id', $subthems);
        },
        'subtheme' => function ($q, $value) {
            $q->where('sub_thematic_area_id', $value);
        },
        'user_id' => function ($q, $value) {
            $q->where('user_id', $value);
        },
        'category' => function ($q, $value) {
            $q->where('publication_catgory_id', $value);
        },
        'tag' => function ($q, $value) {
            $taggedpubs = PublicationTag::where('id', $value)->pluck('publication_id');
            $q->whereIn('id', $taggedpubs);
        }

    ];

    foreach ($filters as $key => $callback) {
        
        if ($request->filled($key)) {
            $callback($query, $request->$key);
        }
    }
}

// Lightweight method for simple queries
public function getLightweight(Request $request, $return_array = false)
{
    $rows_count = $request->rows ?? 20;

    $pubs = Publication::with([
            'file_type', 'author', 'sub_theme', 'category', 'country', 'comments', 'versioning', 'parent'
        ])
        ->withCount('comments')
        ->where('is_version', 0)
        ->where('is_admin_only_access', 0)
        ->where('is_active', 'Active')
        ->where('is_approved', 1);

        if($request->filled('area')){
            $pubs->where('geographical_coverage_id', $request->area)
            ->orWhereHas('countries', function($subQuery) use ($request) {
                $subQuery->where('country.id', $request->area);
            })
            ->orWhereHas('author', function($subQuery) use ($request) {
                $subQuery->where('country_id', $request->area);
            });
        }

    if ($request->order_by_visits) {
        $pubs->orderBy('visits', 'desc');
    } else {
        $pubs->orderBy('id', 'desc');
    }

    if ($request->filled('term')) {
        $pubs->searchTerm($request->term);
    }

    $results = $pubs->paginate($rows_count);
    
    return $return_array ? $results : $results;
}




}