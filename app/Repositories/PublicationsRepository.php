<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Author;
use App\Models\Country;
use App\Models\Favourite;
use App\Models\GeoCoverage;
use App\Models\Publication;
use App\Models\PublicationApprovalLog;
use App\Models\PublicationAttachment;
use App\Models\PublicationComment;
use App\Models\PublicationCommunityOfPractice;
use App\Models\PublicationCountry;
use App\Models\PublicationStaging;
use App\Models\PublicationSummary;
use App\Models\PublicationTag;
use App\Models\PublicationType;
use App\Models\Region;
use App\Models\SubjectArea;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\CommunityOfPracticeMembers;
use App\Models\User;
use App\Support\CommunityTargeting;
use App\Support\SeoSlugger;
use App\Models\ContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use App\Imports\PublicationImport;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use DB;

class PublicationsRepository extends SharedRepo{
    
public function get(Request $request, $return_array = false, $featured = false,$pending=false)
{
    $rows_count = $request->rows ?? 20;

    $with = ['file_type', 'author', 'sub_theme', 'category', 'country', 'comments', 'versioning', 'parent'];
    if (!empty($request->approved_only)) {
        $with[] = 'approver';
        $with[] = 'rejector';
    }
    $pubs = Publication::with($with)
    ->where('is_version', 0);
    if ($request->order_by_latest) {
        $pubs->orderByRaw('COALESCE(date_created, created_at) DESC')->orderByDesc('id');
    } elseif ($request->order_by_visits) {
        $pubs->orderBy('visits', 'desc')->orderBy('id', 'desc');
    } else {
        $pubs->orderBy('id', 'desc');
    }
    $pubs->searchTerm($request->term);

    if ($featured && current_user() && !$request->boolean('homepage_featured_strict')) {
        $user = current_user();

        // "Your Interests" from profile preferences
        $preferencesCacheKey = "user_preferences_{$user->id}";
        $subthemes = cache()->remember($preferencesCacheKey, 3600, function() use ($user) {
            return $user->preferences()->pluck('subtheme_id');
        });

        // Favorite-tag affinity for recommendation expansion
        $favoriteTagsCacheKey = "user_favorite_tag_ids_{$user->id}";
        $favoriteTagIds = cache()->remember($favoriteTagsCacheKey, 1800, function () use ($user) {
            return DB::table('favourites')
                ->join('publication_tags', 'favourites.publication_id', '=', 'publication_tags.publication_id')
                ->where('favourites.user_id', $user->id)
                ->distinct()
                ->pluck('publication_tags.tag_id');
        });

        // Recommendation set for API/web featured feeds:
        // featured OR matching profile interests OR matching favorite tags.
        // Keep featured first in ordering.
        $pubs->where(function ($q) use ($subthemes, $favoriteTagIds) {
            $q->where('is_featured', 1);

            if ($subthemes && count($subthemes) > 0) {
                $q->orWhereIn('sub_thematic_area_id', $subthemes);
            }

            if ($favoriteTagIds && count($favoriteTagIds) > 0) {
                $q->orWhereHas('tags', function ($tq) use ($favoriteTagIds) {
                    $tq->whereIn('tag_id', $favoriteTagIds);
                });
            }
        });

        // Featured publications should always appear first.
        $pubs->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END');
    } elseif ($featured) {
        $pubs->where('is_featured', 1);
    }

    // Random order for general browsing; skip when we need a stable ranking (e.g. homepage Top Searches by visits).
    if (!$featured && !$request->boolean('skip_random_order')) {
        $pubs->inRandomOrder();
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
                      ->orWhere('also_public_on_hub', 1)
                      ->orWhere('user_id', $user->id);
                });
            }, function ($query) use ($request) {
                $query->whereHas('communities', function ($q) use ($request) {
                    $q->where('community_of_practice_id', $request->community_id);
                });
            });
        } 
        else {
            $query->where(function ($q) {
                $q->whereDoesntHave('communities')
                    ->orWhere('also_public_on_hub', 1);
            });
        }
    }, function ($query) {
        $this->access_filter($query);
    });

    if($pending) {
        $pubs->where('is_approved', 0);
    }

    // Filter only approved publications if requested (for admin manage publications page)
    if ($request->approved_only) {
        $pubs->where('is_approved', 1)->where('is_rejected', 0);
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
            ->whereHas('favourited', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('id', 'desc');

        $result = $pubs->paginate($rows_count);

        return $result;
    }

    public function recommendedByPreferences($user_id, $limit = 10, bool $stableRanking = false){
        $user = \App\Models\User::find($user_id);
        if (!$user) {
            return collect();
        }

        $subthemes = $user->preferences()->pluck('subtheme_id');
        if ($subthemes->isEmpty()) {
            return collect();
        }

        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])
            ->where('is_version', 0)
            ->where('is_active', 'Active')
            ->where('is_approved', 1)
            ->whereIn('sub_thematic_area_id', $subthemes)
            ->whereDoesntHave('favourited', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });

        if (!is_admin()) {
            $pubs->where('is_admin_only_access', 0);
        }

        return $pubs->orderByRaw('COALESCE(date_created, created_at) DESC')->orderByDesc('id')->take($limit)->get();
    }

    public function relatedByFavoriteTags($user_id, $limit = 10, bool $stableRanking = false){
        // Get tag IDs from user's favorited publications
        $favoriteTagIds = DB::table('favourites')
            ->join('publication_tags', 'favourites.publication_id', '=', 'publication_tags.publication_id')
            ->where('favourites.user_id', $user_id)
            ->distinct()
            ->pluck('publication_tags.tag_id');

        if ($favoriteTagIds->isEmpty()) {
            return collect();
        }

        // Get publications with these tags (excluding already favorited ones)
        $pubs = Publication::with(['file_type','author','sub_theme','category','comments'])
            ->where('is_version', 0)
            ->where('is_active', 'Active')
            ->where('is_approved', 1)
            ->whereHas('tags', function($q) use ($favoriteTagIds) {
                $q->whereIn('tag_id', $favoriteTagIds);
            })
            ->whereDoesntHave('favourited', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });

        if (!is_admin()) {
            $pubs->where('is_admin_only_access', 0);
        }

        return $pubs->orderByRaw('COALESCE(date_created, created_at) DESC')->orderByDesc('id')->take($limit)->get();
    }

    /**
     * Homepage "Recommended" strip: featured plus preference- and favorite-tag–based pools when logged in,
     * merged and ordered newest first.
     */
    public function homeRecommendedPublications(Request $request, ?int $userId, int $limit = 6): Collection
    {
        $poolSize = max($limit * 6, 36);

        $featuredRequest = clone $request;
        $featuredRequest->merge([
            'is_featured' => 1,
            'rows' => $poolSize,
            'order_by_latest' => true,
            'homepage_featured_strict' => true,
        ]);

        $featuredPool = collect($this->get($featuredRequest, false, true)->items());

        if ($userId) {
            $prefs = $this->recommendedByPreferences($userId, $poolSize);
            $tags = $this->relatedByFavoriteTags($userId, $poolSize);
            $merged = $featuredPool->concat($prefs)->concat($tags)->unique('id');
        } else {
            $merged = $featuredPool;
        }

        return $this->sortPublicationsByLatest($merged)->take($limit)->values();
    }

    /**
     * Recommended feed for infinite scroll: same merge rules as {@see homeRecommendedPublications} but returns one page
     * with stable newest-first ordering. Pool size is capped per request for performance.
     *
     * @return array{items: Collection, has_more: bool, ranking_total: int}
     */
    public function homeRecommendedPublicationsPage(Request $request, ?int $userId, int $perPage, int $page): array
    {
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $need = $page * $perPage;
        $poolSize = min(max($need * 6, 36), 600);

        $featuredRequest = clone $request;
        $featuredRequest->merge([
            'is_featured' => 1,
            'rows' => $poolSize,
            'order_by_latest' => true,
            'homepage_featured_strict' => true,
        ]);

        $featuredPool = collect($this->get($featuredRequest, false, true)->items());

        if ($userId) {
            $prefs = $this->recommendedByPreferences($userId, $poolSize, true);
            $tags = $this->relatedByFavoriteTags($userId, $poolSize, true);
            $merged = $featuredPool->concat($prefs)->concat($tags)->unique('id');
        } else {
            $merged = $featuredPool;
        }

        $sorted = $this->sortPublicationsByLatest($merged);
        $rankingTotal = $sorted->count();
        $pageItems = $sorted->slice($offset, $perPage)->values();
        $hasMore = $rankingTotal > ($page * $perPage);

        return [
            'items' => $pageItems,
            'has_more' => $hasMore,
            'ranking_total' => $rankingTotal,
        ];
    }

    /**
     * @param  Collection|array<int, Publication>  $publications
     */
    protected function sortPublicationsByLatest($publications): Collection
    {
        return Collection::make($publications)->sort(function ($a, $b) {
            $aTime = Carbon::parse($a->date_created ?? $a->created_at ?? 0)->timestamp;
            $bTime = Carbon::parse($b->date_created ?? $b->created_at ?? 0)->timestamp;

            if ($aTime !== $bTime) {
                return $bTime <=> $aTime;
            }

            return ($b->id ?? 0) <=> ($a->id ?? 0);
        })->values();
    }

    /**
     * Prefer one publication per parent theme first (in list order), then fill remaining slots.
     *
     * @param  Collection|array<int, Publication>  $candidates
     */
    protected function diversifyPublicationsByThematicArea($candidates, int $limit): Collection
    {
        $candidates = Collection::make($candidates)->values();
        if ($candidates->isEmpty()) {
            return collect();
        }

        $pubs = \Illuminate\Database\Eloquent\Collection::make($candidates->all());
        $pubs->loadMissing('sub_theme');

        $thematicKey = function ($pub) {
            $id = optional($pub->sub_theme)->thematic_area_id;

            return $id !== null ? (int) $id : 0;
        };

        $picked = collect();
        $seenIds = [];
        $usedTheme = [];

        foreach ($pubs as $pub) {
            if ($picked->count() >= $limit) {
                break;
            }
            $t = $thematicKey($pub);
            if (isset($usedTheme[$t])) {
                continue;
            }
            $usedTheme[$t] = true;
            $picked->push($pub);
            $seenIds[$pub->id] = true;
        }

        foreach ($pubs as $pub) {
            if ($picked->count() >= $limit) {
                break;
            }
            if (!empty($seenIds[$pub->id])) {
                continue;
            }
            $picked->push($pub);
            $seenIds[$pub->id] = true;
        }

        return $picked->take($limit)->values();
    }

    public function find_type($id){
        return PublicationType::find($id);
    }

    public function find_shortened($id){
        return PublicationSummary::find($id);
    }

    public function save(Request $request){

        Log::info("Request:: ". json_encode($request->all()));

        $wasNewPublication = empty($request->id);

        // When creating a version from an existing resource, always create a new record
        if ($request->original_id) {
            $request['id'] = null; // prevent overwriting parent
        }

        $pub  = ($request->id)? Publication::find($request->id):new Publication();
        $user = $request->user_id
            ? User::find($request->user_id)
            : ($request->user('api') ?? $request->user() ?? auth()->user());
  
        if($request->original_id):

            $parent = $this->find($request->original_id,false);
            $pub->sub_thematic_area_id     = $parent->sub_thematic_area_id;
            $pub->geographical_coverage_id = $parent->geographical_coverage_id;
            $pub->is_version = 1;
            $pub->parent_id = $parent->id; // link to parent resource
            $pub->title                    = format_title_with_ai_fallback($parent->title ?? '');
            $versions_now = count($parent->versioning);
            $pub->version_no  = ($request->version)?$request->version:(($versions_now ==0)?$versions_now +2: $versions_now+1);
            $request['category_id']= $parent->data_category_id;
            $request['data_category_id'] = $parent->publication_catgory_id;
            
        else:
            $pub->sub_thematic_area_id      = $request->sub_theme;

            if (! $request->countries) {
                if (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled() && function_exists('hub_owner_country_id') && hub_owner_country_id()) {
                    $pub->geographical_coverage_id = hub_owner_country_id();
                } else {
                    $geo_id = ($user && $user->country_id && $user->area) ? $user->area->id : 1;
                    $pub->geographical_coverage_id = $geo_id;
                }
            } else {
                $pub->geographical_coverage_id = $request->countries[0];
            }
            
            $pub->title                     = format_title_with_ai_fallback($request->title ?? '');

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
        $pub->title                     = format_title_with_ai_fallback($request->title ?? '');
        $pub->description               = sanitize_rich_text_for_storage(clean_unicode($request->description ?? ''));
        $pub->associated_authors        = clean_unicode($request->associated_authors ?? '');
        $pub->author_affiliation        = clean_unicode($request->author_affiliation ?? '');
        $pub->publication               = clean_unicode($request->link ?? '');
        $pub->publication_catgory_id    = $request->data_category_id;
        $pub->visits                    = ($request->id)?$pub->visits:0;
        $pub->data_category_id          = $request->category_id;
        $pub->publication_sub_category_id = $request->publication_sub_category_id ?: null;
        $pub->is_embedded               = $request->is_embedded ?? false;
        $pub->is_default_in_category    = $request->is_default ?? false;
        $pub->is_admin_only_access      = $request->admin_only ?? false;
        $pub->show_disclaimer            = $request->show_disclaimer ?? false;

        CommunityTargeting::mergeTagAllIntoRequest($request);
        if (! $request->id || $request->has('community_targeting_options')) {
            $pub->also_public_on_hub = CommunityTargeting::wantsAlsoPublicOnHubWithCommunities($request) ? 1 : 0;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'public_availability')) {
            $pub->public_availability = resolve_public_availability_from_request(
                $request,
                $request->id ? ($pub->public_availability ?? null) : null
            );
        }

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

            // When `author` is sent, legacy code sets geo from the linked portal user on that author
            // record (hasOne). Many authors have no linked user, or the id may be wrong — avoid 500.
            if ($request->filled('author')) {
                $authorRecord = Author::find($request->author);
                if ($authorRecord && $authorRecord->user && $authorRecord->user->country_id) {
                    $pub->geographical_coverage_id = (int) $authorRecord->user->country_id;
                } elseif ($request->filled('countries') && is_array($request->countries) && count($request->countries) > 0) {
                    $pub->geographical_coverage_id = (int) $request->countries[0];
                }
            }
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
                    if (Schema::hasColumn($pub->getTable(), 'approved_by') && current_user()) {
                        $pub->approved_by = current_user()->id;
                    }
                    if (Schema::hasColumn($pub->getTable(), 'rejected_by')) {
                        $pub->rejected_by = null;
                    }
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
                    
                    $storagePath = hub_storage_path('uploads/publications').'/';
                    
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

        if ((int) ($pub->is_version ?? 0) === 0 && Schema::hasColumn('publication', 'slug') && empty($pub->slug)) {
            $pub->slug = SeoSlugger::forPublication((string) ($pub->title ?? ''), $pub->id ?: null);
        }

        if (Schema::hasColumn('publication', 'content_updated_at')) {
            $pub->content_updated_at = now();
        }

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

        $attachment_path = $attachment_path ? hub_storage_path('uploads/publications').'/'.$attachment_path : null;
        $file_type = get_file_type($attachment_path,$request->link);

        // Detect video from file type name, mime types, link platform, or direct file extensions.
        $isVideoType = false;
        if ($file_type) {
            $typeName = strtolower((string) ($file_type->name ?? ''));
            $mimeList = strtolower((string) ($file_type->mime_types ?? ''));
            $isVideoType = (strpos($typeName, 'video') !== false) || (strpos($mimeList, 'video') !== false);
        }
        $isVideoLink = is_video_platform_url((string) ($request->link ?? ''));
        $isVideo = $isVideoType || $isVideoLink;

        $pub->is_video = $isVideo ? 1 : 0;
        // Video links/files should embed by default for better UX.
        if ($isVideo) {
            $pub->is_embedded = 1;
        }

        // Auto-generate video cover (platform thumbnail or first frame around 2s)
        // when user did not explicitly provide a cover.
        $hasManualCover = $request->hasFile('cover') || ($request->has('cover_url') && !empty($request->cover_url));
        if ($isVideo && !$hasManualCover) {
            $rawExistingCover = (string) ($pub->getRawOriginal('cover') ?? '');
            $isDefaultCover = ($rawExistingCover === '' || $rawExistingCover === 'cover.jpg');
            if (!$request->id || $isDefaultCover) {
                $videoCover = get_video_cover_source((string) ($request->link ?? ''), $attachment_path, 'publication-' . $id);
                if ($videoCover && !empty($videoCover['cover'])) {
                    $pub->cover = $videoCover['cover'];
                    $pub->cover_is_exteranl = !empty($videoCover['is_external']);
                }
            }
        }

        // Non-video: first PDF attachment → first page as cover (web wizard does this client-side; API does it here).
        if (! $isVideo && $saved && ! $hasManualCover && $id) {
            $rawExistingCover = (string) ($pub->getRawOriginal('cover') ?? '');
            $isDefaultCover = ($rawExistingCover === '' || $rawExistingCover === 'cover.jpg');
            if ($isDefaultCover) {
                $firstPdfRow = PublicationAttachment::query()
                    ->where('publication_id', $id)
                    ->orderBy('id')
                    ->get()
                    ->first(function (PublicationAttachment $row) {
                        return publication_filename_is_pdf((string) $row->getRawOriginal('file'));
                    });
                if ($firstPdfRow) {
                    $pdfBasename = basename((string) $firstPdfRow->getRawOriginal('file'));
                    $pdfPath = hub_storage_path('uploads/publications').'/'.$pdfBasename;
                    if (is_file($pdfPath)) {
                        $outDir = hub_storage_path('uploads/publications');
                        $coverBasename = $this->extractPublicationCoverJpegFromPdf($pdfPath, $outDir);
                        if ($coverBasename !== null) {
                            $pub->cover = $coverBasename;
                            $pub->cover_is_exteranl = false;
                        }
                    }
                }
            }
        }
         
        $pub->file_type_id =$file_type->id; //$request->file_type;
        $pub->update();
      
        // Attach communities (after mergeTagAllIntoRequest).
        if ($saved && ($request->has('communities') || $request->boolean('tag_all_my_communities'))) {
            $raw = $request->input('communities', []);
            $communities = is_array($raw) ? $raw : ($raw !== null && $raw !== '' ? [$raw] : []);
            $valid = array_values(array_filter($communities, function ($c) {
                return ! empty($c) && $c !== null && $c !== '' && strtolower((string) $c) !== 'all' && is_numeric($c);
            }));
            if (! empty($valid)) {
                $this->attach_to_community($valid, $id);
            } else {
                PublicationCommunityOfPractice::where('publication_id', $id)->delete();
                $pub->also_public_on_hub = 0;
                $pub->save();
                \Log::info('Communities cleared - publication visible to everyone', [
                    'publication_id' => $id,
                ]);
            }
        }

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
        
        // Send notification to approvers if publication is pending approval (only for new submissions, not edits)
        if ($saved && !$request->id && !is_admin() && $pub->is_approved == 0 && empty($request->from_rss_staging_id)) {
            // Reload publication with author relationship
            $pub->load('author');
            
            // Build approval URL
            $approveUrl = url('admin/publications/details') . '?id=' . $pub->id;
            
            // Dispatch notification to approvers
            \App\Jobs\NotifyApprovers::dispatch(
                'publication',
                $pub->id,
                $pub->title ?? 'Untitled Publication',
                $pub->description ?? '',
                $pub->author->name ?? ($pub->user->name ?? 'Unknown'),
                $approveUrl
            )->onQueue('default');
        }

        // When approving from RSS staging: mark publication as RSS and update staging record
        if ($saved && !empty($request->from_rss_staging_id)) {
            $staging = PublicationStaging::find($request->from_rss_staging_id);
            if ($staging) {
                $pub->is_rss = true;
                $pub->rss_id = $staging->rss_feed_id;
                $pub->save();
                $staging->update([
                    'processed_at' => now(),
                    'processed_status' => PublicationStaging::STATUS_APPROVED,
                    'publication_id' => $pub->id,
                ]);
            }
        }

        if ($saved && !$request->original_id && $wasNewPublication) {
            if ((int) ($pub->is_approved ?? 0) === 1) {
                $this->logPublicationApprovalEvent(
                    (int) $pub->id,
                    'auto_approved',
                    null,
                    ['source' => is_admin() ? 'admin_submission' : 'privileged_role']
                );
            } elseif ((int) ($pub->is_approved ?? 0) === 0 && (int) ($pub->is_rejected ?? 0) === 0) {
                $this->logPublicationApprovalEvent(
                    (int) $pub->id,
                    'submitted',
                    null,
                    ['submitter_user_id' => $pub->user_id]
                );
            }
        }

        return $pub;
    }

    public function attach_countries($publication,$request){
        
        // Normalize inputs to arrays
        $rccs = (is_array($request->rccs))?$request->rccs:json_decode($request->rccs, true);
        $countries = (is_array($request->countries))?$request->countries:json_decode($request->countries, true);
        
        // Ensure arrays
        if (!is_array($rccs)) $rccs = [];
        if (!is_array($countries)) $countries = [];
        
        // Debug logging
        \Log::info('attach_countries called', [
            'publication_id' => $publication->id ?? null,
            'rccs_raw' => $request->rccs ?? null,
            'rccs_processed' => $rccs,
            'countries_raw' => $request->countries ?? null,
            'countries_processed' => $countries,
        ]);
        
        $countryIds = [];
        $regionIds = [];

        // Filter out "all" and invalid values from region selections
        $rccsFiltered = array_filter($rccs, function($rcc) {
            return $rcc !== 'all' && $rcc !== '' && !empty($rcc) && is_numeric($rcc);
        });
        
        // Filter out "all" and invalid values from country selections (needed for fallback check)
        $countriesFiltered = array_filter($countries, function($country) {
            return $country !== 'all' && !empty($country) && is_numeric($country);
        });
        
        // Check if "all" is selected in regions (can be with or without other regions)
        // Check multiple formats: 'all', 'All', case-insensitive
        $hasAllRegions = false;
        if (!empty($rccs)) {
            foreach ($rccs as $rcc) {
                $rccLower = is_string($rcc) ? strtolower(trim($rcc)) : '';
                if ($rccLower === 'all' || $rcc === 'all' || $rcc === 'All') {
                    $hasAllRegions = true;
                    break;
                }
            }
        }
        
        // Check if "all" is selected in countries (direct selection or all countries auto-selected)
        $hasAllCountries = false;
        if (!empty($countries)) {
            foreach ($countries as $country) {
                $countryLower = is_string($country) ? strtolower(trim($country)) : '';
                if ($countryLower === 'all' || $country === 'all' || $country === 'All') {
                    $hasAllCountries = true;
                    break;
                }
            }
            
            // Fallback: If "all" regions is selected and many/all countries are auto-selected,
            // treat it as "all" countries (typically 54-55 AU member states)
            if (!$hasAllCountries && $hasAllRegions && count($countriesFiltered) >= 50) {
                $totalCountriesCount = Country::where('region_id', '>', 0)->count();
                // If the selected countries are close to or equal to total countries, treat as "all"
                if (count($countriesFiltered) >= ($totalCountriesCount * 0.9)) {
                    $hasAllCountries = true;
                }
            }
        }
        
        \Log::info('attach_countries checks', [
            'hasAllRegions' => $hasAllRegions,
            'hasAllCountries' => $hasAllCountries,
            'rccs' => $rccs,
            'rccsFiltered' => $rccsFiltered,
            'countries_count' => count($countries ?? []),
            'countriesFiltered_count' => count($countriesFiltered ?? []),
        ]);

        // Case 1: "all" regions selected (alone or with other regions) → select ALL countries globally
        // When "all" regions is selected, it means all countries regardless of country selection
        if ($hasAllRegions) {
            // Get all countries that belong to regions (countries always have region_id > 0)
            $countryIds = Country::where('region_id', '>', 0)->pluck('id')->toArray();
            \Log::info('attach_countries: Case 1 - All regions selected', [
                'countryIds_count' => count($countryIds),
            ]);
        }
        // Case 2: Specific region(s) selected (one or more, but NOT "all")
        elseif (!empty($rccsFiltered)) {
            // Get valid region IDs
            $regionIds = Region::whereIn('id', $rccsFiltered)->pluck('id')->toArray();
            
            if (!empty($regionIds)) {
                // IMPORTANT: When specific regions are selected, "all" countries means all countries in those regions ONLY
                // Check if "all" countries is selected first
                if ($hasAllCountries) {
                    // "all" countries with specific regions → get all countries from those specific regions only
                $countryIds = Country::whereIn('region_id', $regionIds)->pluck('id')->toArray();
                    \Log::info('attach_countries: Case 2a - Specific regions with "all" countries (scoped to regions)', [
                        'regionIds' => $regionIds,
                        'countryIds_count' => count($countryIds),
                    ]);
                }
                // If specific countries are manually selected (not "all"), validate they belong to selected regions
                elseif (!empty($countriesFiltered)) {
                    // Validate that manually selected countries belong to at least one of the selected regions
                    // A resource can belong to multiple regions but not all countries in those regions
                    $validCountryIds = Country::whereIn('id', $countriesFiltered)
                        ->whereIn('region_id', $regionIds)
                        ->pluck('id')
                        ->toArray();
                    
                    $countryIds = array_values($validCountryIds);
                    \Log::info('attach_countries: Case 2b - Specific regions with specific countries', [
                        'regionIds' => $regionIds,
                        'countryIds_count' => count($countryIds),
                    ]);
                } else {
                    // No countries selected at all → get all countries from ALL selected regions
                    // This handles: specific regions selected but no countries specified
                    $countryIds = Country::whereIn('region_id', $regionIds)->pluck('id')->toArray();
                    \Log::info('attach_countries: Case 2c - Specific regions, no countries specified', [
                        'regionIds' => $regionIds,
                        'countryIds_count' => count($countryIds),
                    ]);
                }
            } else {
                // No valid regions found, but countries were selected
                // Since countries must belong to regions, validate countries have valid region_id
                if (!empty($countriesFiltered)) {
                    $validCountryIds = Country::whereIn('id', $countriesFiltered)
                        ->where('region_id', '>', 0)
                        ->pluck('id')
                        ->toArray();
                    $countryIds = array_values($validCountryIds);
                }
            }
        }
        // Case 3: Only "all" countries selected (no regions) → select ALL countries globally
        elseif ($hasAllCountries && empty($rccsFiltered)) {
            // This case is when user selects "all" countries but no regions
            // Get all countries that belong to regions
            $countryIds = Country::where('region_id', '>', 0)->pluck('id')->toArray();
            \Log::info('attach_countries: Case 3 - "All" countries selected without regions', [
                'countryIds_count' => count($countryIds),
            ]);
        }
        // Case 3: Only countries selected (no regions) - validate they belong to regions
        elseif (!empty($countriesFiltered)) {
            // Ensure all selected countries have valid region_id (countries are chained to regions)
            $validCountryIds = Country::whereIn('id', $countriesFiltered)
                ->where('region_id', '>', 0)
                ->pluck('id')
                ->toArray();
            $countryIds = array_values($validCountryIds);
        }
        
        // Sync countries (replace existing, no duplicates)
        if (!empty($countryIds)) {
            // Remove duplicates and ensure all IDs are integers
            $countryIds = array_unique(array_map('intval', $countryIds));
            \Log::info('attach_countries: Syncing countries', [
                'publication_id' => $publication->id ?? null,
                'countryIds_count' => count($countryIds),
                'countryIds_sample' => array_slice($countryIds, 0, 10),
            ]);
            $publication->countries()->sync($countryIds);
        } else {
            // If no countries selected, detach all
            \Log::warning('attach_countries: No countries to sync, detaching all', [
                'publication_id' => $publication->id ?? null,
            ]);
            $publication->countries()->sync([]);
        }
        
        \Log::info('attach_countries: Completed', [
            'publication_id' => $publication->id ?? null,
            'final_countries_count' => $publication->countries()->count(),
        ]);
    }

    public function findBySlug(string $slug, $update_visits = true)
    {
        $id = Publication::query()
            ->where('slug', $slug)
            ->where('is_version', 0)
            ->value('id');

        if (! $id) {
            return null;
        }

        return $this->find($id, $update_visits);
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

            if (! $viewed) {
                publication_record_visit_metrics((int) $pub->id, true);
                set_cookie('Viewed'.$pub->id, 'yes');
            } else {
                publication_touch_last_visited((int) $pub->id);
            }
        endif;

        return $pub;
    }

    public function delete($id){
        $publication = Publication::find($id);
        if (!$publication) {
            return false;
        }

        if (!$this->publicationIsPendingDeletion($publication)) {
            return false;
        }

        return $publication->delete();
    }

    public function publicationIsPendingDeletion(Publication $publication): bool
    {
        return (int) ($publication->is_version ?? 0) === 0
            && (int) ($publication->is_approved ?? 0) === 0
            && (int) ($publication->is_rejected ?? 0) === 0;
    }

    public function get_tags(){
        return Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc')->get();
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
        $comunities = json_decode($comunities, true);

        if(is_array($comunities)):
            // Filter out null, empty, "all", and invalid values
            // Empty values represent "All" which means visible to everyone (no specific communities)
            $validCommunities = array_filter($comunities, function($community_id) {
                return !empty($community_id) && 
                       $community_id !== null && 
                       $community_id !== '' && 
                       strtolower($community_id) !== 'all' &&
                       is_numeric($community_id);
            });
            
            if (empty($validCommunities)) {
                \Log::info('No valid communities to attach', [
                    'publication_id' => $publication_id,
                    'original_communities' => $comunities
                ]);
                return; // Don't try to insert empty data
            }
            
            // Optimized: Use bulk insert instead of individual inserts in a loop
            $communityData = [];
            foreach($validCommunities as $community_id) {
                $communityData[] = [
                    'community_of_practice_id' => intval($community_id),
                    'publication_id' => $publication_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            if (!empty($communityData)) {
                // Delete existing communities first to avoid duplicates
                PublicationCommunityOfPractice::where('publication_id', $publication_id)->delete();
                
                PublicationCommunityOfPractice::insert($communityData);
                
                \Log::info('Communities attached successfully', [
                    'publication_id' => $publication_id,
                    'communities_count' => count($communityData),
                    'community_ids' => array_column($communityData, 'community_of_practice_id')
                ]);
                
                // Send notifications to community members
                $publication = Publication::with('author')->find($publication_id);
                if ($publication) {
                    \App\Jobs\NotifyCommunityMembers::dispatch(
                        $validCommunities,
                        'publication',
                        $publication_id,
                        $publication->title ?? 'Untitled Publication',
                        $publication->description ?? '',
                        $publication->author->name ?? current_user()->name ?? 'Unknown',
                        (int) ($publication->user_id ?? 0) ?: null
                    )->onQueue('default');
                }
            }
       endif;
    }
    catch(\Exception $exception){
            Log::error("Error occured". $exception->getMessage(), [
                'publication_id' => $publication_id,
                'communities' => $comunities ?? null,
                'exception' => $exception->getTraceAsString()
            ]);
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

        $userId = auth()->id();
        if (! $userId) {
            return;
        }

        $exists = Favourite::where('user_id', $userId)->where('publication_id', $pub_id)->exists();
        if ($exists) {
            return;
        }

        $fav = new Favourite();
        $fav->user_id = $userId;
        $fav->publication_id = $pub_id;
        $fav->save();
    }

    public function remove_favourite($pub_id){

        $userId = auth()->id();
        if (! $userId) {
            return;
        }

        Favourite::where('publication_id', $pub_id)->where('user_id', $userId)->delete();
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
                if (! \App\Support\PublicationAttachmentSecurity::isAllowedUpload($file)) {
                    \Log::warning('Blocked unsafe publication attachment upload', [
                        'publication_id' => $publication_id,
                        'file_name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                    ]);
                    $errorCount++;
                    continue;
                }

                $storagePath = hub_storage_path('uploads/publications').'/';
                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $clientOriginal = basename((string) $file->getClientOriginalName());
                $clientOriginal = trim(preg_replace('/\s+/u', ' ', $clientOriginal));
                if ($clientOriginal === '' || $clientOriginal === '.') {
                    $clientOriginal = 'attachment.' . ($file->guessExtension() ?: 'bin');
                }
                $clientOriginal = \Illuminate\Support\Str::limit($clientOriginal, 255, '');

                $file_path = $file->hashName();
                if (file_exists($storagePath . $file_path)) {
                    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                    $file_path = \Illuminate\Support\Str::uuid()->toString() . ($ext !== '' ? '.' . $ext : '');
                }

                $file->move($storagePath, $file_path);

                $displayLabel = \Illuminate\Support\Str::limit($clientOriginal, 120);

            // Optimized: Collect attachment data for bulk insert
            if($publication_id) {
                $attachmentData[] = [
                    'description' => $displayLabel,
                    'original_filename' => $clientOriginal,
                    'file' => $file_path,
                    'publication_id' => $publication_id,
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
            $file->move(hub_storage_path('uploads/publications/summaries').'/',$file_path);
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
    $comment->comment = sanitize_rich_text_for_storage(clean_unicode($raw));
    
    // Check if auto-approve comments is enabled (defaults to true)
    $autoApprove = settings()->auto_approve_comments ?? true;
    if ($autoApprove) {
        $comment->status = 'approved';
    }
    
    $comment->save();

    return $comment;
}

public function change_approval_status(Request $request){

   
    $publication = ($request->is_summary)?PublicationSummary::find($request->id):Publication::find($request->id);
    
    // Load relationships for email notification
    if (!$request->is_summary) {
        $publication->load(['user', 'author']);
    } else {
        if (method_exists($publication, 'load')) {
            $publication->load('user');
        }
    }

    $previousState = [
        'is_approved' => (int) ($publication->is_approved ?? 0),
        'is_rejected' => (int) ($publication->is_rejected ?? 0),
        'is_active' => $publication->is_active ?? null,
    ];
    
    if($request->approved){

     $publication->is_approved= 1;
     $publication->is_rejected= 0;
     if (Schema::hasColumn($publication->getTable(), 'approved_by')) {
         $publication->approved_by = current_user() ? current_user()->id : null;
     }
     if (Schema::hasColumn($publication->getTable(), 'rejected_by')) {
         $publication->rejected_by = null;
     }
     if (Schema::hasColumn($publication->getTable(), 'rejected_reason')) {
         $publication->rejected_reason = null;
     }
     if (Schema::hasColumn($publication->getTable(), 'rejected_at')) {
         $publication->rejected_at = null;
     }

     if(!$request->is_summary)
     $publication->is_active= 'Active';

     $action = "Approved";

    }
    else if($request->rejected){

     $publication->is_rejected= 1;
     $publication->is_approved= 0;
     if (Schema::hasColumn($publication->getTable(), 'rejected_by')) {
         $publication->rejected_by = current_user() ? current_user()->id : null;
     }
     if (Schema::hasColumn($publication->getTable(), 'approved_by')) {
         $publication->approved_by = null;
     }
     if (Schema::hasColumn($publication->getTable(), 'rejected_reason')) {
         $publication->rejected_reason = $request->input('rejected_reason');
     }
     if (Schema::hasColumn($publication->getTable(), 'rejected_at')) {
         $publication->rejected_at = now();
     }

     if(!$request->is_summary)
     $publication->is_active= 'In-Active';

     $action = "Rejected";

    }

    $publication->update();

    if (!$request->is_summary && $publication instanceof Publication) {
        if ($request->approved) {
            $this->logPublicationApprovalEvent(
                (int) $publication->id,
                'approved',
                null,
                ['previous' => $previousState]
            );
        } elseif ($request->rejected) {
            $this->logPublicationApprovalEvent(
                (int) $publication->id,
                'rejected',
                trim((string) $request->input('rejected_reason', '')) ?: null,
                ['previous' => $previousState]
            );
        }
    }
    
    // Send email notification using proper template
    $userEmail = null;
    $userName = null;
    
    if (!$request->is_summary && $publication instanceof \App\Models\Publication) {
        // For publications, try to get email from user or author
        if ($publication->user && $publication->user->email) {
            $userEmail = $publication->user->email;
            $userName = $publication->user->name ?? 'Member';
        } elseif ($publication->author && $publication->author->email) {
            $userEmail = $publication->author->email;
            $userName = $publication->author->name ?? 'Member';
        }
    } else {
        // For summaries, try to get email from user
        if (isset($publication->user) && $publication->user && $publication->user->email) {
            $userEmail = $publication->user->email;
            $userName = $publication->user->name ?? 'Member';
        }
    }
    
    if ($userEmail) {
        if ($action === 'Approved') {
            $subject = 'Publication Approved: ' . ($publication->title ?? 'Your Resource');
            
            $publicationUrl = !$request->is_summary 
                ? publication_url($publication)
                : url('admin/publications/summaries');
            
            $body = view('emails.publication_approved', [
                'userName' => $userName,
                'publicationTitle' => $publication->title ?? 'Your Resource',
                'publicationDescription' => $publication->description ?? '',
                'publicationUrl' => $publicationUrl,
                'isSummary' => $request->is_summary ?? false,
            ])->render();
        } else {
            // Rejected - use simple format for now (can create a template later)
            $reason = $request->input('rejected_reason');
            $msg = 'We are sorry to inform you that your publication has been rejected';
            $body = $reason ? ($msg . ' Reason: ' . $reason) : $msg;
        }
        
        $emailData = (object) [
            'email' => $userEmail,
            'subject' => $subject ?? "Resource " . ($publication->title ?? '') . " has been $action",
            'body' => $body,
            'title' => $subject ?? "Resource " . ($publication->title ?? '') . " has been $action"
        ];
        
        SendMailJob::dispatch($emailData)->onQueue('default');
    }

    return $publication;
}

public function approve_comment($id){

    $comment = PublicationComment::find($id);
    $comment->status = 'approved';
    $comment->update();

    $email = optional($comment->user)->email;
    if ($email && trim($email) !== '') {
        $alert = array(
            'title' => "Comment  $comment->comment has been Approved",
            'body'=>'We are happy to inform you that your comment has been approved',
            'email'=>$email
        );

        SendMailJob::dispatch( $alert)->onQueue('default');
    } else {
        \Log::warning('Publication comment approve: no author email, notification skipped', ['comment_id' => $id]);
    }

}

public function reject_comment($id){

    $comment = PublicationComment::find($id);
    $comment->status='rejected';
    $comment->update();

    $email = optional($comment->user)->email;
    if ($email && trim($email) !== '') {
        $alert = array(
            'title' => "Comment  $comment->comment has been Rejected",
            'body'=>'We are sorry to inform you that your comment has been rejected',
            'email'=>$email
        );

        SendMailJob::dispatch( $alert)->onQueue('default');
    } else {
        \Log::warning('Publication comment reject: no author email, notification skipped', ['comment_id' => $id]);
    }

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

/**
 * Normalize request input to a list of positive integers, or null when absent / "all" / empty (no facet restriction).
 *
 * @return list<int>|null
 */
private function normalizeMultiFilterIds($request, string $key): ?array
{
    $raw = $request->input($key);
    if ($raw === null || $raw === '' || $raw === 'all') {
        return null;
    }
    if (! is_array($raw)) {
        $raw = [$raw];
    }
    $ids = array_values(array_unique(array_filter(array_map('intval', $raw), function ($id) {
        return $id > 0;
    })));

    return $ids === [] ? null : $ids;
}

// New method to apply filters
private function applyFilters($query, $request) {

    $skipCategory = false;
    $skipFileCategory = false;
    $skipFileType = false;

    $dcIds = $this->normalizeMultiFilterIds($request, 'data_category_id');
    if ($dcIds !== null) {
        $query->whereIn('publication_catgory_id', $dcIds);
        $skipCategory = true;
    }

    $fcIds = $this->normalizeMultiFilterIds($request, 'file_category_id');
    if ($fcIds !== null) {
        $query->whereIn('data_category_id', $fcIds);
        $skipFileCategory = true;
    }

    $ftIds = $this->normalizeMultiFilterIds($request, 'file_type_id');
    if ($ftIds === null) {
        $ftIds = $this->normalizeMultiFilterIds($request, 'file_type');
    }
    if ($ftIds !== null) {
        $query->whereIn('file_type_id', $ftIds);
        $skipFileType = true;
    }

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
        'file_category_id' => function ($q, $value) {
            $q->where('data_category_id', $value);
        },
        'tag' => function ($q, $value) {
            $taggedpubs = PublicationTag::where('tag_id', $value)->pluck('publication_id');
            $q->whereIn('id', $taggedpubs);
        }

    ];

    $aliases = [
        'author' => ['author', 'author_id'],
        'subtheme' => ['subtheme', 'sub_thematic_area_id'],
        'category' => ['category', 'data_category_id'],
        'file_type' => ['file_type', 'file_type_id'],
    ];

    foreach ($filters as $key => $callback) {
        if ($key === 'category' && $skipCategory) {
            continue;
        }
        if ($key === 'file_category_id' && $skipFileCategory) {
            continue;
        }
        if ($key === 'file_type' && $skipFileType) {
            continue;
        }
        $value = null;
        if (isset($aliases[$key])) {
            foreach ($aliases[$key] as $param) {
                $v = $request->input($param);
                if (is_array($v)) {
                    continue;
                }
                if ($v !== null && $v !== '' && $v !== 'all') {
                    $value = $v;
                    break;
                }
            }
        } else {
            $value = $request->input($key);
        }
        if (is_array($value)) {
            continue;
        }
        if ($value !== null && $value !== '' && $value !== 'all') {
            $callback($query, $value);
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

    /**
     * Render PDF first page to a JPEG in $outputDir; returns stored basename for publication.cover (Imagick).
     */
    private function extractPublicationCoverJpegFromPdf(string $pdfAbsolutePath, string $outputDir): ?string
    {
        if (! extension_loaded('imagick') || ! class_exists(\Imagick::class)) {
            Log::info('PDF cover extraction skipped: Imagick extension not available');

            return null;
        }
        if (! is_readable($pdfAbsolutePath)) {
            return null;
        }
        try {
            $imagick = new \Imagick();
            $imagick->setResolution(144, 144);
            $imagick->readImage($pdfAbsolutePath.'[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(85);
            $stem = pathinfo($pdfAbsolutePath, PATHINFO_FILENAME);
            $basename = $stem.'_cover_'.substr(md5_file($pdfAbsolutePath), 0, 12).'.jpg';
            $outPath = rtrim($outputDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$basename;
            $imagick->writeImage($outPath);
            $imagick->clear();
            $imagick->destroy();

            return is_file($outPath) ? $basename : null;
        } catch (\Throwable $e) {
            Log::warning('PDF first-page cover extraction failed', [
                'path' => $pdfAbsolutePath,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

public function bulkInactive($ids)
{
    Publication::whereIn('id', $ids)->update(['is_active' => 'In-Active']);
}

public function bulkDelete($ids)
{
    Publication::query()
        ->whereIn('id', $ids)
        ->where('is_version', 0)
        ->where('is_approved', 0)
        ->where('is_rejected', 0)
        ->delete();
}

public function bulkFeatured($ids)
{
    Publication::whereIn('id', $ids)->update(['is_featured' => 1]);
}

public function togglePublicationFeatured(int $id): ?Publication
{
    $publication = Publication::find($id);
    if (!$publication) {
        return null;
    }

    $publication->is_featured = (int) ($publication->is_featured ?? 0) === 1 ? 0 : 1;
    $publication->save();

    return $publication;
}

public function togglePublicationActive(int $id): ?Publication
{
    $publication = Publication::find($id);
    if (!$publication) {
        return null;
    }

    $isActive = strtolower((string) ($publication->is_active ?? '')) === 'active';
    $publication->is_active = $isActive ? 'In-Active' : 'Active';
    $publication->save();

    return $publication;
}

    /**
     * Summary counts for the admin Manage Publications index page.
     */
    public function adminPublicationIndexStats(): array
    {
        $base = Publication::query()->where('is_version', 0);

        return [
            'approved' => (clone $base)->where('is_approved', 1)->where('is_rejected', 0)->count(),
            'pending' => (clone $base)->where('is_approved', 0)->where('is_rejected', 0)->count(),
            'featured' => (clone $base)->where('is_approved', 1)->where('is_rejected', 0)->where('is_featured', 1)->count(),
            'inactive' => (clone $base)
                ->where('is_approved', 1)
                ->where('is_rejected', 0)
                ->whereRaw("LOWER(COALESCE(is_active, '')) != 'active'")
                ->count(),
        ];
    }

    /**
     * Base query for admin "Manage Publications" (approved, non-version rows).
     */
    public function buildAdminApprovedQuery(Request $request)
    {
        $pubs = Publication::query()
            ->with(['author', 'country', 'approver', 'rejector', 'user'])
            ->where('is_version', 0)
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->orderBy('id', 'desc');

        if ($request->filled('term')) {
            $pubs->searchTerm($request->term);
        }

        $this->applyFilters($pubs, $request);
        $this->access_filter($pubs);

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $pubs->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('author_affiliation', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($aq) use ($search) {
                        $aq->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        return $pubs;
    }

    public function logPublicationApprovalEvent(
        int $publicationId,
        string $action,
        ?string $reason = null,
        ?array $metadata = null,
        ?int $performedBy = null
    ): PublicationApprovalLog {
        $actor = $performedBy ?? (current_user() ? (int) current_user()->id : null);
        $actorName = null;
        if ($actor) {
            $actorName = User::query()->whereKey($actor)->value('name');
        }

        return PublicationApprovalLog::create([
            'publication_id' => $publicationId,
            'action' => $action,
            'performed_by' => $actor,
            'performed_by_name' => $actorName,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, PublicationApprovalLog|object>
     */
    public function approvalTrailForPublication(Publication $publication)
    {
        $logs = PublicationApprovalLog::query()
            ->where('publication_id', $publication->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        if ($logs->isNotEmpty()) {
            return $logs;
        }

        return $this->synthesizeLegacyApprovalTrail($publication);
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected function synthesizeLegacyApprovalTrail(Publication $publication): Collection
    {
        $publication->loadMissing(['user', 'approver', 'rejector']);
        $entries = collect();

        if ($publication->user) {
            $entries->push((object) [
                'action' => 'submitted',
                'action_label' => 'Submitted for review',
                'performed_by_name' => $publication->user->name,
                'reason' => null,
                'created_at' => $publication->created_at,
                'is_legacy' => true,
            ]);
        }

        if ((int) ($publication->is_rejected ?? 0) === 1) {
            $entries->push((object) [
                'action' => 'rejected',
                'action_label' => 'Rejected',
                'performed_by_name' => $publication->rejector->name
                    ?? ($publication->rejected_by ? 'User #'.$publication->rejected_by : 'Moderator not recorded'),
                'reason' => $publication->rejected_reason,
                'created_at' => $publication->rejected_at ?? $publication->updated_at,
                'is_legacy' => true,
            ]);
        } elseif ((int) ($publication->is_approved ?? 0) === 1) {
            $entries->push((object) [
                'action' => !empty($publication->approved_by) ? 'approved' : 'legacy_approved',
                'action_label' => !empty($publication->approved_by) ? 'Approved' : 'Approved (moderator not recorded)',
                'performed_by_name' => $publication->approver->name
                    ?? 'Moderator not recorded — may predate approval tracking',
                'reason' => null,
                'created_at' => $publication->updated_at ?? $publication->created_at,
                'is_legacy' => true,
            ]);
        }

        return $entries->sortByDesc(function ($entry) {
            return $entry->created_at;
        })->values();
    }

    public function moderatorDisplayName(Publication $publication): string
    {
        if ((int) ($publication->is_rejected ?? 0) === 1) {
            if (!empty($publication->rejected_by) && $publication->rejector) {
                return 'Rejected by '.($publication->rejector->name ?? 'Unknown');
            }

            return 'Rejected (moderator not recorded)';
        }

        if ((int) ($publication->is_approved ?? 0) === 1) {
            if (!empty($publication->approved_by) && $publication->approver) {
                return 'Approved by '.($publication->approver->name ?? 'Unknown');
            }

            return 'Approved (moderator not recorded)';
        }

        return '—';
    }

    /**
     * Server-side DataTables payload for admin publications index.
     */
    public function adminApprovedDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 20);
        if ($length <= 0) {
            $length = 20;
        }
        $length = min($length, 100);

        $base = $this->buildAdminApprovedQuery($request);
        $recordsTotal = Publication::query()
            ->where('is_version', 0)
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 2);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [
            1 => 'id',
            2 => 'title',
            3 => 'description',
            7 => 'is_active',
            8 => 'date_created',
        ];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        if ($orderCol === 'date_created') {
            $base->orderByRaw('COALESCE(date_created, created_at) '.$orderDir);
        } else {
            $base->orderBy($orderCol, $orderDir);
        }

        $rows = $base->skip($start)->take($length)->get();
        $currentUserId = current_user() ? current_user()->id : null;
        $isAdmin = is_admin();

        $data = [];
        $index = $start + 1;
        foreach ($rows as $publication) {
            $isInactive = strtolower((string) ($publication->is_active ?? '')) !== 'active';
            $isFeatured = (int) ($publication->is_featured ?? 0) === 1;
            $rowClass = $isInactive ? 'pub-row-inactive' : ($isFeatured ? 'pub-row-featured' : '');

            $dateCreated = '-';
            if ($publication->date_created) {
                $dateCreated = Carbon::parse($publication->date_created)->format('M d, Y');
            } elseif ($publication->created_at) {
                $dateCreated = Carbon::parse($publication->created_at)->format('M d, Y');
            }

            $title = '<div class="pub-cell-wrap"><a href="'.e($publication->publication).'" target="_blank" rel="noopener" class="pub-title-link">'.e($publication->title).'</a></div>';
            $description = $this->adminPublicationDescriptionCell($publication->title, $publication->description);
            $author = '<div class="pub-cell-wrap">'.e($publication->author->name ?? '').'</div>';
            $affiliation = '<div class="pub-cell-wrap">'.e($publication->author_affiliation ?: '-').'</div>';
            $memberState = e($publication->country->name ?? '');
            $status = e(get_publication_state($publication->is_approved, $publication->is_rejected));
            $moderator = '<div class="pub-cell-wrap"><span class="text-muted">'.e($this->moderatorDisplayName($publication)).'</span>'
                .' <a href="'.url('admin/publications/details').'?id='.$publication->id.'#approval-trail" class="small" title="View approval trail">Trail</a></div>';

            $actions = '<div class="pub-actions-group">'
                .'<a href="'.publication_url($publication).'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success" title="Public view"><i class="fa fa-external-link-alt"></i></a>'
                .'<a href="'.url('admin/publications/details').'?id='.$publication->id.'" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>';
            if ($publication->user_id == $currentUserId || $isAdmin) {
                $actions .= '<a href="'.url('admin/publications/edit').'?id='.$publication->id.'" class="btn btn-sm btn-outline-dark" title="Edit"><i class="fa fa-edit"></i></a>';
            }

            $featuredBtnClass = $isFeatured ? 'btn-warning' : 'btn-outline-warning';
            $featuredTitle = $isFeatured ? 'Remove from featured' : 'Mark as featured';
            $featuredIconClass = $isFeatured ? 'fa-solid fa-star' : 'fa-regular fa-star';
            $actions .= '<button type="button" class="btn btn-sm '.$featuredBtnClass.' pub-toggle-featured" data-id="'.$publication->id.'" data-featured="'.($isFeatured ? '1' : '0').'" title="'.$featuredTitle.'"><i class="'.$featuredIconClass.'"></i></button>';

            $activeTitle = $isInactive ? 'Publish' : 'Unpublish';
            $activeBtnClass = $isInactive ? 'btn-outline-success' : 'btn-outline-secondary';
            $activeIcon = $isInactive ? 'fa-upload' : 'fa-ban';
            $actions .= '<button type="button" class="btn btn-sm '.$activeBtnClass.' pub-toggle-active" data-id="'.$publication->id.'" data-active="'.($isInactive ? '0' : '1').'" title="'.$activeTitle.'"><i class="fa '.$activeIcon.'"></i></button>';
            $actions .= '</div>';

            $data[] = [
                'DT_RowClass' => trim($rowClass),
                'checkbox' => '<input type="checkbox" name="selected_ids[]" value="'.$publication->id.'" class="publication-checkbox">',
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => $title,
                'description' => $description,
                'author' => $author,
                'affiliation' => $affiliation,
                'member_state' => $memberState,
                'status' => $status,
                'date_created' => $dateCreated,
                'moderator' => $moderator,
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * Truncated description for admin tables with optional full-text preview modal.
     */
    private function adminPublicationDescriptionCell(?string $publicationTitle, ?string $rawDescription, int $wordLimit = 31): string
    {
        $full = trim(html_to_text((string) $rawDescription));
        if ($full === '') {
            return '<div class="pub-cell-wrap">—</div>';
        }

        $words = preg_split('/\s+/u', $full, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $isTruncated = count($words) > $wordLimit;
        $preview = $isTruncated
            ? implode(' ', array_slice($words, 0, $wordLimit))
            : $full;

        $html = '<div class="pub-cell-wrap"><span class="pub-desc-excerpt">'.e($preview).'</span>';

        if ($isTruncated) {
            $titleAttr = e($publicationTitle ?: 'Publication description');
            $descAttr = e(json_encode($full, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT));
            $html .= ' <button type="button" class="btn btn-link btn-sm p-0 align-baseline pub-desc-preview"'
                .' data-title="'.$titleAttr.'"'
                .' data-description="'.$descAttr.'"'
                .'>Preview</button>';
        }

        return $html.'</div>';
    }

    public function buildAdminPendingQuery(Request $request)
    {
        $pubs = Publication::query()
            ->with(['author', 'country'])
            ->where('is_version', 0)
            ->where('is_approved', 0)
            ->where('is_rejected', 0)
            ->orderBy('id', 'desc');

        if ($request->filled('term')) {
            $pubs->searchTerm($request->term);
        }

        $this->applyFilters($pubs, $request);
        $this->access_filter($pubs);

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $pubs->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($aq) use ($search) {
                        $aq->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        return $pubs;
    }

    public function adminPendingDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 20)), 100);

        $base = $this->buildAdminPendingQuery($request);
        $recordsTotal = Publication::query()
            ->where('is_version', 0)
            ->where('is_approved', 0)
            ->where('is_rejected', 0)
            ->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [1 => 'id', 2 => 'title', 3 => 'description', 7 => 'is_active', 8 => 'date_created'];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        if ($orderCol === 'date_created') {
            $base->orderByRaw('COALESCE(date_created, created_at) '.$orderDir);
        } else {
            $base->orderBy($orderCol, $orderDir);
        }

        $rows = $base->skip($start)->take($length)->get();
        $canDelete = auth()->user() && auth()->user()->can('delete_publications');
        $currentUserId = current_user() ? current_user()->id : null;
        $isAdmin = is_admin();

        $data = [];
        $index = $start + 1;
        foreach ($rows as $publication) {
            $dateCreated = '-';
            if ($publication->date_created) {
                $dateCreated = Carbon::parse($publication->date_created)->format('M d, Y');
            } elseif ($publication->created_at) {
                $dateCreated = Carbon::parse($publication->created_at)->format('M d, Y');
            }

            $actions = '<a href="'.url('admin/publications/details').'?id='.$publication->id.'" class="btn btn-sm btn-outline-primary mr-1" title="View"><i class="fa fa-eye"></i></a>';
            if ($publication->user_id == $currentUserId || $isAdmin) {
                $actions .= '<a href="'.url('admin/publications/edit').'?id='.$publication->id.'" class="btn btn-sm btn-outline-dark mr-1" title="Edit"><i class="fa fa-edit"></i></a>';
            }
            if ($canDelete) {
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(\''.$publication->id.'\')" title="Delete"><i class="fa fa-trash"></i></button>';
            }

            $data[] = [
                'checkbox' => '<input type="checkbox" name="publication_ids[]" value="'.$publication->id.'" class="pending-pub-cb">',
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => '<a href="'.e($publication->publication).'" target="_blank" rel="noopener">'.truncate($publication->title, 30).'</a>',
                'description' => truncate(html_to_text($publication->description), 50),
                'author' => e($publication->author->name ?? ''),
                'affiliation' => e($publication->author_affiliation ?: '-'),
                'member_state' => e($publication->country->name ?? ''),
                'status' => e(get_publication_state($publication->is_approved, $publication->is_rejected)),
                'date_created' => $dateCreated,
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function buildAdminSummariesQuery(Request $request)
    {
        $qry = PublicationSummary::query()->with('author')->orderBy('id', 'desc');

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            $qry->where(function ($q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if ($request->filled('author')) {
            $qry->where('author_id', $request->author);
        }

        return $qry;
    }

    public function adminSummariesDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 15)), 100);

        $base = $this->buildAdminSummariesQuery($request);
        $recordsTotal = PublicationSummary::query()->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [0 => 'id', 1 => 'title', 2 => 'description', 4 => 'is_approved'];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        $index = $start + 1;
        foreach ($rows as $row) {
            if ((int) ($row->is_approved ?? 0) === 0 && (int) ($row->is_rejected ?? 0) === 0) {
                $status = 'Pending Approval';
            } elseif ((int) ($row->is_rejected ?? 0) === 1) {
                $status = 'Rejected';
            } else {
                $status = 'Approved';
            }

            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => truncate($row->title, 30),
                'content' => truncate(html_to_text($row->description), 50),
                'author' => e($row->author->name ?? ''),
                'status' => e($status),
                'actions' => '<a href="'.url('admin/publications/summary').'?id='.$row->id.'" class="btn btn-sm btn-outline-primary mr-1"><i class="fa fa-eye mr-1"></i> Details</a>'
                    .'<a href="'.url('admin/publications/details').'?id='.$row->resource_id.'" class="btn btn-sm btn-outline-dark"><i class="fa fa-external-link mr-1"></i> Original</a>',
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function buildAdminModerateCommentsQuery(Request $request)
    {
        $qry = PublicationComment::query()
            ->with(['user', 'publication'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            $qry->where(function ($q) use ($term) {
                $q->where('comment', 'like', '%'.$term.'%')
                    ->orWhereHas('publication', function ($pq) use ($term) {
                        $pq->where('title', 'like', '%'.$term.'%');
                    });
            });
        }

        return $qry;
    }

    public function adminModerateCommentsDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 20)), 100);

        $base = $this->buildAdminModerateCommentsQuery($request);
        $recordsTotal = PublicationComment::query()->where('status', 'pending')->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 3);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [0 => 'publication_id', 1 => 'comment', 3 => 'created_at'];
        $orderCol = $orderMap[$orderColIndex] ?? 'created_at';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $comment) {
            $pubTitle = $comment->publication ? truncate($comment->publication->title, 40) : '—';
            $createdAt = $comment->created_at ? Carbon::parse($comment->created_at)->format('M d, Y H:i') : '—';
            $actions = '<a href="'.url('admin/publications/approve_comment').'?id='.$comment->id.'" class="btn btn-sm btn-outline-success approve_comment mr-1"><i class="fa fa-check mr-1"></i> Approve</a>'
                .'<a href="'.url('admin/publications/reject_comment').'?id='.$comment->id.'" class="btn btn-sm btn-outline-danger reject_comment"><i class="fa fa-times mr-1"></i> Reject</a>';

            $data[] = [
                'publication' => e($pubTitle),
                'comment' => e(truncate($comment->comment, 120)),
                'created_by' => e($comment->user->name ?? '—'),
                'created_at' => e($createdAt),
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function buildAdminDashboardRecentQuery(Request $request)
    {
        $pubs = Publication::query()
            ->with(['author'])
            ->where('is_version', 0)
            ->orderBy('visits', 'desc')
            ->orderBy('id', 'desc');

        $this->access_filter($pubs);

        $term = trim((string) ($request->input('search.q')
            ?: $request->input('search[title]')
            ?: $request->input('search.title')));
        if ($term === '' && $request->filled('search.author')) {
            $term = trim((string) $request->input('search.author'));
        }
        if ($term === '' && $request->filled('search.description')) {
            $term = trim((string) $request->input('search.description'));
        }
        if ($term !== '') {
            $this->applyDashboardResourceSearch($pubs, $term);
        }

        return $pubs;
    }

    private function applyDashboardResourceSearch($query, string $term): void
    {
        $safeTerm = addcslashes($term, '%_');
        $query->where(function ($q) use ($term, $safeTerm) {
            $q->where('title', 'like', '%'.$safeTerm.'%')
                ->orWhere('description', 'like', '%'.$safeTerm.'%')
                ->orWhere('author_affiliation', 'like', '%'.$safeTerm.'%')
                ->orWhere('associated_authors', 'like', '%'.$safeTerm.'%')
                ->orWhereHas('author', function ($aq) use ($safeTerm) {
                    $aq->where('name', 'like', '%'.$safeTerm.'%');
                })
                ->orWhereHas('country', function ($cq) use ($safeTerm) {
                    $cq->where('name', 'like', '%'.$safeTerm.'%');
                })
                ->orWhereHas('countries', function ($cq) use ($safeTerm) {
                    $cq->where('name', 'like', '%'.$safeTerm.'%');
                })
                ->orWhereIn('id', PublicationCountry::query()
                    ->whereIn('country_id', Country::query()
                        ->whereHas('region', function ($rq) use ($safeTerm) {
                            $rq->where('region_name', 'like', '%'.$safeTerm.'%');
                        })
                        ->pluck('id'))
                    ->pluck('publication_id'))
                ->orWhereHas('sub_theme', function ($sq) use ($safeTerm) {
                    $sq->where('description', 'like', '%'.$safeTerm.'%')
                        ->orWhereHas('theme', function ($tq) use ($safeTerm) {
                            $tq->where('description', 'like', '%'.$safeTerm.'%');
                        });
                })
                ->orWhereHas('data_category', function ($dq) use ($safeTerm) {
                    $dq->where('name', 'like', '%'.$safeTerm.'%');
                })
                ->orWhereIn('id', PublicationTag::query()
                    ->whereIn('tag_id', Tag::query()->where('name', 'like', '%'.$safeTerm.'%')->pluck('id'))
                    ->pluck('publication_id'));

            if (function_exists('states_enabled') && states_enabled()) {
                $q->orWhereIn('geographical_coverage_id', GeoCoverage::query()
                    ->where('name', 'like', '%'.$safeTerm.'%')
                    ->pluck('id'));
            }

            $words = array_filter(preg_split('/\s+/u', trim($term), -1, PREG_SPLIT_NO_EMPTY), function ($w) {
                return strlen($w) > 1;
            });
            if (count($words) > 1) {
                $q->orWhere(function ($sub) use ($words) {
                    foreach ($words as $w) {
                        $sub->where('associated_authors', 'like', '%'.$w.'%');
                    }
                });
            }
        });
    }

    public function adminDashboardRecentDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 10)), 100);

        $base = $this->buildAdminDashboardRecentQuery($request);
        $recordsTotal = Publication::query()->where('is_version', 0)->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [1 => 'created_at', 2 => 'title', 5 => 'is_approved'];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        $data = [];
        $index = $start + 1;

        foreach ($rows as $publication) {
            $created = $publication->created_at
                ? Carbon::parse($publication->created_at)->format('M d, Y')
                : '-';
            $title = '<div class="pub-cell-wrap"><a href="'.e($publication->publication ?? publication_url($publication)).'" target="_blank" rel="noopener" class="pub-title-link">'.e($publication->title).'</a></div>';
            $description = $this->adminPublicationDescriptionCell($publication->title, $publication->description);
            $author = '<div class="pub-cell-wrap">'.e($publication->author->name ?? '-').'</div>';

            if ($publication->is_approved) {
                $statusClass = 'success';
                $statusLabel = 'Approved';
            } elseif ($publication->is_rejected) {
                $statusClass = 'danger';
                $statusLabel = 'Rejected';
            } else {
                $statusClass = 'warning';
                $statusLabel = 'Pending';
            }
            $status = '<span class="badge badge-'.$statusClass.'">'.e($statusLabel).'</span>';

            $actions = '<a href="'.publication_url($publication).'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="Preview"><i class="fa fa-eye mr-1"></i>Preview</a>';

            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'date_created' => e($created),
                'title' => $title,
                'description' => $description,
                'author' => $author,
                'status' => $status,
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function buildAccountMyPublicationsQuery(Request $request, int $userId)
    {
        $query = Publication::query()
            ->with(['author'])
            ->where('user_id', $userId);

        $term = trim((string) ($request->input('term') ?: $request->input('search.value')));
        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if ($request->filled('search.title')) {
            $query->where('title', 'like', '%'.trim((string) $request->input('search.title')).'%');
        }
        if ($request->filled('search.description')) {
            $query->where('description', 'like', '%'.trim((string) $request->input('search.description')).'%');
        }

        $status = strtolower(trim((string) $request->input('status', '')));
        if ($status === 'approved') {
            $query->where('is_approved', 1)->where('is_rejected', 0);
        } elseif ($status === 'pending') {
            $query->where('is_approved', 0)->where('is_rejected', 0);
        } elseif ($status === 'rejected') {
            $query->where('is_rejected', 1);
        }

        return $query;
    }

    public function accountMyPublicationsDatatable(Request $request): array
    {
        $userId = (int) auth()->id();
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 10)), 100);

        $base = $this->buildAccountMyPublicationsQuery($request, $userId);
        $recordsTotal = Publication::query()->where('user_id', $userId)->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 5);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [1 => 'title', 2 => 'description', 3 => 'is_approved', 4 => 'visits', 5 => 'created_at'];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        $data = [];
        $index = $start + 1;

        foreach ($rows as $row) {
            $status = get_publication_state($row->is_approved, $row->is_rejected);
            if ((int) ($row->is_rejected ?? 0) === 1) {
                $statusClass = 'danger';
            } elseif ((int) ($row->is_approved ?? 0) === 1) {
                $statusClass = 'success';
            } else {
                $statusClass = 'warning';
            }
            $statusBadge = '<span class="badge badge-'.$statusClass.'">'.e($status).'</span>';

            $title = '<div class="pub-cell-wrap"><a href="'.e($row->publication).'" target="_blank" rel="noopener" class="pub-title-link">'.e($row->title).'</a></div>';
            $description = $this->adminPublicationDescriptionCell($row->title, $row->description);
            if ((int) ($row->is_rejected ?? 0) === 1 && !empty($row->rejected_reason)) {
                $description .= '<div class="mt-2 p-2" style="background:#fef2f2;border:1px solid #fecaca;border-radius:0;"><small class="text-danger"><strong>Rejection reason:</strong> '.e($row->rejected_reason).'</small></div>';
            }

            $totalViews = \App\Models\PublicationView::getTotalViews($row->id);
            $isApproved = (int) ($row->is_approved ?? 0) === 1;

            $actions = '<div class="btn-group btn-group-sm" role="group" aria-label="Actions">'
                .'<a href="'.publication_url($row).'" class="btn btn-outline-secondary" target="_blank" rel="noopener"><i class="fa fa-eye"></i> Preview</a>';
            if (!$isApproved) {
                $actions .= '<a href="'.route('account.publications.edit').'?ref='.$row->id.'" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Edit</a>'
                    .'<a href="javascript:void(0);" onclick="openDeleteModal('.$row->id.')" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</a>';
            }
            $actions .= '</div>';

            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => $title,
                'description' => $description,
                'status' => $statusBadge,
                'views' => '<span class="badge badge-info">'.number_format($totalViews).'</span>',
                'created_at' => e($row->created_at ? $row->created_at->format('M d, Y') : 'N/A'),
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

}