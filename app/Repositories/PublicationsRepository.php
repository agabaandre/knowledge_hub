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
use App\Models\PublicationCountry;
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
        if($request->term){
            $t = trim($request->term);
            $pubs->where(function($q) use ($t){
                $q->where('title','like','%'.$t.'%')
                  ->orWhere('description','like','%'.$t.'%');
            });
        }
        $result = $pubs->paginate($rows_count)->appends($request->all());

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

        // When creating a version from an existing resource, always create a new record
        if ($request->original_id) {
            $request['id'] = null; // prevent overwriting parent
        }

        $pub  = ($request->id)? Publication::find($request->id):new Publication();
        $user = ($request->user_id)?User::find($request->user_id):auth()->user();
  
        if($request->original_id):

            $parent = $this->find($request->original_id,false);
            $pub->sub_thematic_area_id     = $parent->sub_thematic_area_id;
            $pub->geographical_coverage_id = $parent->geographical_coverage_id;
            $pub->is_version = 1;
            $pub->parent_id = $parent->id; // link to parent resource
            $pub->title                    = clean_unicode($parent->title ?? ''); // Clean Unicode from parent title
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
            
            $pub->title                     = clean_unicode($request->title ?? '');

        endif;
        
        $pub->user_id              = $user->id;
        $pub->author_id            = ($request->author)?$request->author: $user->author_id;
        
        if ($request->has('year_published')) {
            $pub->year_published = intval($request->year_published) ?: null;
        } elseif (!$request->id && !$request->original_id) {
            // default for new records when not provided
            $pub->year_published = intval(date('Y'));
        }
        
        // Clean Unicode characters from text fields before saving
        $pub->title                     = clean_unicode($request->title ?? '');
        $pub->description               = clean_unicode($request->description ?? '');
        $pub->associated_authors        = clean_unicode($request->associated_authors ?? '');
        $pub->author_affiliation        = clean_unicode($request->author_affiliation ?? '');
        $pub->publication               = clean_unicode($request->link ?? '');
        $pub->publication_catgory_id    = $request->data_category_id;
        $pub->visits                    = ($request->id)?$pub->visits:0;
        $pub->data_category_id          = $request->category_id;
        $pub->is_embedded               = $request->is_embedded ?? false;
        $pub->is_default_in_category    = $request->is_default ?? false;
        $pub->is_admin_only_access      = $request->admin_only ?? false;
        $pub->show_disclaimer            = $request->show_disclaimer ?? false;

        // Publication metadata fields - clean Unicode
        $pub->doi                       = clean_unicode($request->doi ?? null);
        $pub->issn                      = clean_unicode($request->issn ?? null);
        $pub->isbn                      = clean_unicode($request->isbn ?? null);
        $pub->publisher                 = clean_unicode($request->publisher ?? null);
        $pub->license_id                = $request->license_id ?? null;
        $pub->copyright_info            = clean_unicode($request->copyright_info ?? null);
        $pub->funder                    = clean_unicode($request->funder ?? null);
        
        // Journal fields - clean Unicode
        $pub->journal_name              = clean_unicode($request->journal_name ?? null);
        $pub->journal_volume            = clean_unicode($request->journal_volume ?? null);
        $pub->journal_issue             = clean_unicode($request->journal_issue ?? null);
        $pub->journal_pages             = clean_unicode($request->journal_pages ?? null);


        if(!is_admin()){

            $pub->is_active   = 'In-Active';
            $pub->is_approved = 0;
            $pub->is_rejected = 0;

            if($request->author)
            $pub->geographical_coverage_id  = Author::find($request->author)->user->country_id;
        }
        else {
            // Auto-publish when submitted by privileged roles (IDs), configurable via ENV
            try {
                $autoRoleIds = collect(explode(',', env('AUTO_PUBLISH_ROLE_IDS', '')))
                    ->filter(function($v){ return trim($v) !== ''; })
                    ->map(function($v){ return (int) trim($v); });

                $hasAutoRole = false;
                if ($user && method_exists($user, 'roles')) {
                    $userRoleIds = $user->roles ? $user->roles->pluck('id') : collect();
                    $hasAutoRole = $autoRoleIds->isNotEmpty() && $userRoleIds->intersect($autoRoleIds)->isNotEmpty();
                }

                if ($hasAutoRole || is_admin()) {
                    $pub->is_active   = 'Active';
                    $pub->is_approved = 1;
                    $pub->is_rejected = 0;
                }
            } catch (\Throwable $e) {
                // no-op; fallback to default behaviour
            }
        }

        //save cover
        if($request->hasFile('cover')):
            // New cover file uploaded
            $file = $request->file('cover');
            
            // Save cover file directly (cover is saved separately, not as an attachment)
            if ($file && $file->isValid()) {
                try {
                    $description = $file->getClientOriginalName();
                    $file_name   = md5_file($file->getRealPath());
                    $extension   = $file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                    $cover_filepath = $file_name.'.'.$extension;
                    
                    $storagePath = storage_path().'/app/public/uploads/publications/';
                    
                    // Ensure directory exists
                    if (!is_dir($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }
                    
                    $file->move($storagePath, $cover_filepath);
                    
                    $pub->cover = $cover_filepath;
                    $pub->cover_is_exteranl = false; // Reset to local file
                    
                    \Log::info('Cover image uploaded', [
                        'publication_id' => $request->id ?? 'new',
                        'cover_filepath' => $cover_filepath,
                        'file_name' => $description
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error saving cover image: ' . $e->getMessage(), [
                        'publication_id' => $request->id ?? 'new',
                        'exception' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::warning('Invalid cover file uploaded', [
                    'publication_id' => $request->id ?? 'new',
                    'file_valid' => $file ? $file->isValid() : 'null'
                ]);
            }
        else:
            // No new cover file uploaded
            if(!$request->id):
                // New publication - set default cover
                $pub->cover = "cover.jpg";
                $pub->cover_is_exteranl = false;
            else:
                // Editing existing publication - preserve existing cover if not explicitly cleared
                // Only update if cover_is_exteranl is being set explicitly
                if ($request->has('cover_is_exteranl')) {
                    $pub->cover_is_exteranl = (bool)$request->cover_is_exteranl;
                }
                // If cover URL is provided for external cover, update it
                if ($request->has('cover_url') && !empty($request->cover_url)) {
                    $pub->cover = $request->cover_url;
                    $pub->cover_is_exteranl = true;
                }
                // Otherwise, keep existing cover (don't overwrite)
                \Log::info('Cover image preserved during edit', [
                    'publication_id' => $request->id,
                    'existing_cover' => $pub->getRawOriginal('cover'),
                    'cover_is_external' => $pub->cover_is_exteranl
                ]);
            endif;
        endif;

        $saved = ($request->id)?$pub->update():$pub->save();

        $id = ($request->id)?$request->id:$pub->id;

        // delete selected existing attachments
        if($request->id && isset($request->remove_attachments) && is_array($request->remove_attachments)){
            PublicationAttachment::where('publication_id', $id)
                ->whereIn('id', $request->remove_attachments)
                ->delete();
        }

        $attachment_path =null;
        //save attachments - handle both single and multiple file uploads
        // Note: Attachments can be saved even when publication has a link (both are allowed)
        
        // Debug: Log all file-related request data
        \Log::info('File upload check', [
            'publication_id' => $id,
            'saved' => $saved,
            'has_files' => $request->hasFile('files'),
            'all_files' => $request->allFiles(),
            'request_keys' => array_keys($request->all())
        ]);
        
        // Check for files in multiple ways to handle different scenarios
        // Note: Laravel automatically handles 'files[]' as 'files' when using hasFile()
        $hasFiles = false;
        $files = null;
        
        // Method 1: Standard Laravel way - handles both 'files' and 'files[]'
        if ($request->hasFile('files')) {
            $hasFiles = true;
            $files = $request->file('files');
        }
        // Method 2: Check for files[] explicitly (sometimes needed for AJAX submissions)
        elseif ($request->hasFile('files.0') || isset($request->allFiles()['files'])) {
            $allFiles = $request->allFiles();
            if (isset($allFiles['files'])) {
                $hasFiles = true;
                $files = $allFiles['files'];
            }
        }
        // Method 3: Check all files as fallback
        elseif (!empty($request->allFiles())) {
            $allFiles = $request->allFiles();
            // Check for 'files' (Laravel normalizes 'files[]' to 'files')
            if (isset($allFiles['files'])) {
                $hasFiles = true;
                $files = $allFiles['files'];
            }
        }
        
        if($saved && $hasFiles):
            try {
                \Log::info('Processing file uploads', [
                    'publication_id' => $id,
                    'has_files' => $hasFiles,
                    'files_is_array' => is_array($files),
                    'files_count' => is_array($files) ? count($files) : ($files ? 1 : 0),
                    'files_type' => gettype($files)
                ]);
                
                // Handle both single file and array of files
                if (is_array($files)) {
                    // Multiple files uploaded - filter out null/invalid entries
                    $filesToProcess = [];
                    foreach ($files as $index => $file) {
                        if ($file && $file->isValid()) {
                            $filesToProcess[] = $file;
                            \Log::debug('Valid file found', [
                                'index' => $index,
                                'name' => $file->getClientOriginalName(),
                                'size' => $file->getSize()
                            ]);
                        } else {
                            \Log::warning('Invalid file skipped', [
                                'index' => $index,
                                'is_null' => is_null($file),
                                'is_valid' => $file ? $file->isValid() : 'N/A'
                            ]);
                        }
                    }
                } else {
                    // Single file uploaded
                    if ($files && $files->isValid()) {
                        $filesToProcess = [$files];
                        \Log::debug('Single valid file found', [
                            'name' => $files->getClientOriginalName(),
                            'size' => $files->getSize()
                        ]);
                    } else {
                        $filesToProcess = [];
                        \Log::warning('Single file invalid', [
                            'is_null' => is_null($files),
                            'is_valid' => $files ? $files->isValid() : 'N/A'
                        ]);
                    }
                }
                
                if (!empty($filesToProcess)) {
                    \Log::info('Saving attachments', [
                        'publication_id' => $id,
                        'files_count' => count($filesToProcess)
                    ]);
                    $attachment_path = $this->save_attachments($filesToProcess, $id);
                } else {
                    \Log::warning('No valid files to process', [
                        'publication_id' => $id,
                        'files_received' => is_array($files) ? count($files) : ($files ? 1 : 0)
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error saving attachments: ' . $e->getMessage(), [
                    'publication_id' => $id,
                    'files_count' => $hasFiles ? (is_array($files) ? count($files) : ($files ? 1 : 0)) : 0,
                    'exception' => $e->getTraceAsString()
                ]);
                // Don't throw - allow publication to save even if attachments fail
            }
        else:
            // Log when files are not present
            if ($saved) {
                \Log::debug('No files uploaded for publication', [
                    'publication_id' => $id,
                    'has_files_method1' => $request->hasFile('files'),
                    'has_files_method2' => !empty($request->allFiles()),
                    'all_files_keys' => array_keys($request->allFiles() ?? []),
                    'upload_type' => $request->input('upload_type', 'unknown'),
                    'request_method' => $request->method()
                ]);
            }
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

        // Save tags - delete old tags first if editing, then save new ones
        // Check for tags in multiple ways to handle different request formats
        $tagsToSave = null;
        
        if ($request->has('tags')) {
            $tagsToSave = $request->input('tags');
        } elseif ($request->has('tags[]')) {
            $tagsToSave = $request->input('tags[]');
        }
        
        \Log::info('Tags save check', [
            'publication_id' => $id,
            'saved' => $saved,
            'has_tags' => $request->has('tags'),
            'has_tags_array' => $request->has('tags[]'),
            'tags_input' => $request->input('tags'),
            'tags_array_input' => $request->input('tags[]'),
            'all_tags' => $tagsToSave,
            'tags_type' => gettype($tagsToSave)
        ]);
        
        if($saved && !empty($tagsToSave)):
            try {
                // Delete existing tags for this publication
                PublicationTag::where('publication_id', $id)->delete();
                
                // Ensure tags is an array
                $tags = is_array($tagsToSave) ? $tagsToSave : (is_string($tagsToSave) ? json_decode($tagsToSave, true) : []);
                
                // Filter out null/empty values
                $tags = array_filter($tags, function($tag_id) {
                    return !empty($tag_id) && $tag_id !== null && $tag_id !== '';
                });
                
                // Reset array keys
                $tags = array_values($tags);
                
                if (!empty($tags)) {
                    // Save new tags
                    $this->save_tags($tags, $id);
                    \Log::info('Tags saved successfully', [
                        'publication_id' => $id,
                        'tags_count' => count($tags),
                        'tag_ids' => $tags
                    ]);
                } else {
                    \Log::warning('Tags array is empty after filtering', [
                        'publication_id' => $id,
                        'original_tags' => $tagsToSave
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error saving tags: ' . $e->getMessage(), [
                    'publication_id' => $id,
                    'tags' => $tagsToSave,
                    'exception' => $e->getTraceAsString()
                ]);
            }
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
            SendMailJob::dispatch( $alert)->onQueue('default');
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

    public function find($id,$update_visits=true){

        $pub = Publication::with([
            'file_type',
            'attachments',
            'author','sub_theme',
            'comments','parent',
            'summaries','versioning',
            'sub_category','data_category',
            'license',
            'countries',
            'tags.tag'])->find($id);

        if($pub && $update_visits):
            $cookie_name = "Viewed".$pub->id.((auth()->user() && auth()->user()->id)?auth()->user()->id :'');
            $viewed      = get_cookie($cookie_name);

            if(!$viewed && $pub):
                $pub->visits = $pub->visits + 1;
                $pub->update();
                set_cookie("Viewed".$pub->id,'yes');
            endif;
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
            // Skip invalid tag IDs
            if (empty($tag_id) || $tag_id === null || $tag_id === '') {
                continue;
            }
            $tagData[] = [
                'tag_id' => intval($tag_id),
                'publication_id' => intval($publication_id)
                // Note: publication_tags table doesn't have timestamps
            ];
        }
        
        if (!empty($tagData)) {
            try {
                PublicationTag::insert($tagData);
                \Log::info('Tags inserted into database', [
                    'publication_id' => $publication_id,
                    'tags_count' => count($tagData),
                    'tag_data' => $tagData
                ]);
            } catch (\Exception $e) {
                \Log::error('Error inserting tags: ' . $e->getMessage(), [
                    'publication_id' => $publication_id,
                    'tag_data' => $tagData,
                    'exception' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } else {
            \Log::warning('No tag data to insert', [
                'publication_id' => $publication_id,
                'original_tags' => $tags
            ]);
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

        if (empty($files) || !$publication_id) {
            \Log::warning('save_attachments called with empty files or no publication_id', [
                'files_count' => is_array($files) ? count($files) : ($files ? 1 : 0),
                'publication_id' => $publication_id
            ]);
            return null;
        }

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        $attachmentData = [];
        $savedCount = 0;
        $errorCount = 0;
        
        foreach ($upfiles as $file) {
            // Skip invalid files
            if (!$file || !$file->isValid()) {
                \Log::warning('Invalid file skipped in save_attachments', [
                    'publication_id' => $publication_id,
                    'file_name' => $file ? $file->getClientOriginalName() : 'null'
                ]);
                $errorCount++;
                continue;
            }

            try {
                $description = $file->getClientOriginalName();
                $file_name   = md5_file($file->getRealPath());
                $extension   = $file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $file_path   = $file_name.'.'.$extension;
                
                $storagePath = storage_path().'/app/public/uploads/publications/';
                
                // Ensure directory exists
                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
               
                $file->move($storagePath, $file_path);

                // Optimized: Collect attachment data for bulk insert
                if($publication_id) {
                    $attachmentData[] = [
                        "description" => $description,
                        "file" => $file_path,
                        "publication_id" => $publication_id
                    ];
                    $savedCount++;
                }
            } catch (\Exception $e) {
                \Log::error('Error processing individual file in save_attachments: ' . $e->getMessage(), [
                    'publication_id' => $publication_id,
                    'file_name' => $file->getClientOriginalName(),
                    'exception' => $e->getTraceAsString()
                ]);
                $errorCount++;
                // Continue processing other files even if one fails
                continue;
            }
        }

        // Optimized: Use bulk insert instead of individual inserts
        if (!empty($attachmentData)) {
            try {
                PublicationAttachment::insert($attachmentData);
                \Log::info('Attachments saved successfully', [
                    'publication_id' => $publication_id,
                    'count' => count($attachmentData),
                    'saved' => $savedCount,
                    'errors' => $errorCount
                ]);
            } catch (\Exception $e) {
                \Log::error('Error inserting attachments: ' . $e->getMessage(), [
                    'publication_id' => $publication_id,
                    'attachment_data' => $attachmentData,
                    'exception' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } else {
            \Log::warning('No attachment data to insert', [
                'publication_id' => $publication_id,
                'files_processed' => count($upfiles),
                'errors' => $errorCount
            ]);
        }

       return $file_path;
    }

    public function save_summary(Request $request){

        $summary = new PublicationSummary();
        $summary->resource_id = $request->original_id;
        $summary->title       = clean_unicode($request->title ?? '');
        $summary->description = clean_unicode($request->summary ?? '');

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

    // Accept content from multiple keys and guard against null/empty
    $raw = $request->input('comment');
    if ($raw === null) {
        $raw = $request->input('commentInput', $request->input('content', $request->input('message')));
    }
    $raw = is_string($raw) ? trim($raw) : '';
    if ($raw === '') {
        abort(422, 'Comment is required');
    }

    $comment = new PublicationComment();

    $comment->user_id = current_user() ? current_user()->id : ($request->user_id ?? null);
    $comment->publication_id = $request->publication_id;
    $comment->comment = clean_unicode($raw);
    $comment->save();

    return $comment;
}

public function change_approval_status(Request $request){

   
    $publication = ($request->is_summary)?PublicationSummary::find($request->id):Publication::find($request->id);
    
    
    if($request->approved){

     $publication->is_approved= 1;
     $publication->is_rejected= 0;
     if(property_exists($publication, 'approved_by')){ $publication->approved_by = current_user()->id; }
     if(property_exists($publication, 'rejected_by')){ $publication->rejected_by = null; }
     if(property_exists($publication, 'rejected_reason')){ $publication->rejected_reason = null; }
     if(property_exists($publication, 'rejected_at')){ $publication->rejected_at = null; }

     if(!$request->is_summary)
     $publication->is_active= 'Active';

     $msg = 'We are happy to inform you that your publication has been approved';
     $action = "Approved";

    }
    else if($request->rejected){

     $publication->is_rejected= 1;
     $publication->is_approved= 0;
     if(property_exists($publication, 'rejected_by')){ $publication->rejected_by = current_user()->id; }
     if(property_exists($publication, 'approved_by')){ $publication->approved_by = null; }
     if(property_exists($publication, 'rejected_reason')){ $publication->rejected_reason = $request->input('rejected_reason'); }
     if(property_exists($publication, 'rejected_at')){ $publication->rejected_at = now(); }

     if(!$request->is_summary)
     $publication->is_active= 'In-Active';

     $action = "Rejected";

     $msg = 'We are sorry to inform you that your publication has been rejected';

    }

    $publication->update();
    
    $reason = $request->input('rejected_reason');
    $body = ($action === 'Rejected' && $reason)
        ? ($msg.' Reason: '.$reason)
        : $msg;
    $alert = array(
        'title' => "Resource  $publication->title has been $action",
        'body'=> $body,
        'email'=>@$publication->user->email
    );
    SendMailJob::dispatch( $alert)->onQueue('default');

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

    SendMailJob::dispatch( $alert)->onQueue('default');

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

    SendMailJob::dispatch( $alert)->onQueue('default');

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
    SendMailJob::dispatch( $alert)->onQueue('default');

    return $record;
}

public function save_content_request(Request $request){

   $record = new ContentRequest();
   $record->subject     = clean_unicode($request->title ?? '');
   $record->description = clean_unicode($request->description ?? '');
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
            // Optimized: Use a more efficient approach instead of whereHas
            $q->where(function($subQuery) use ($value) {
                $country_pubs=   PublicationCountry::where('country_id', $value)->pluck('publication_id');
                $subQuery->whereIn('id', $country_pubs);    
            });
        },
        'rcc' => function ($q, $value) {
            if (states_enabled() && $value !=='all') {
                $country_ids = Country::where('region_id', $value)->pluck('id');
                $q->where(function($subQuery) use ($country_ids) {
                    $country_pubs=   PublicationCountry::whereIn('country_id', $country_ids)->pluck('publication_id');
                    $subQuery->whereIn('id', $country_pubs);
                });
            }
        },
        'country_id' => function ($q, $value) {
            // Optimized: Use a more efficient approach instead of whereHas
            $q->where(function($subQuery) use ($value) {
                $country_pubs=   PublicationCountry::where('country_id', $value)->pluck('publication_id');
                $subQuery->whereIn('id', $country_pubs);
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
            $taggedpubs = PublicationTag::where('tag_id', $value)->pluck('publication_id');
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
        ->where('is_version', 0)
        ->where('is_admin_only_access', 0)
        ->where('is_active', 'Active')
        ->where('is_approved', 1);

    if($request->filled('area')){
        // Optimized: Use a more efficient approach instead of whereHas
        $pubs->where(function($subQuery) use ($request) {
            $country_pubs=   PublicationCountry::where('country_id', $request->area)->pluck('publication_id');
            $subQuery->whereIn('id', $country_pubs);
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

    public function bulkInactive($ids)
    {
        Publication::whereIn('id', $ids)->update(['is_active' => 'In-Active']);
    }

    public function bulkDelete($ids)
    {
        Publication::whereIn('id', $ids)->delete();
    }

    public function bulkFeatured($ids)
    {
        Publication::whereIn('id', $ids)->update(['is_featured' => 1]);
    }

}