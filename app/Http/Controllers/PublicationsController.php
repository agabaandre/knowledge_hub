<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;

class PublicationsController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }

    public function show(Request $request){
        $data['publication'] = $this->publicationsRepo->find($request->id);

        if(!$data['publication'])
            abort(404);
        
        // Get related publications - prioritize by tags, then themes/sub-themes
        $tagIds = $data['publication']->tag_ids ?? [];
        $thematicAreaId = $data['publication']->thematic_area_id ?? null;
        $subThematicAreaId = $data['publication']->sub_thematic_area_id ?? null;
        
        $relatedPubs = collect();
        
        // Priority 1: Publications with matching tags (up to 10)
        if (!empty($tagIds)) {
            $taggedPubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('tag_id', $tagIds);
                })
                ->with(['author', 'tags.tag']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $taggedPubsQuery->where('is_admin_only_access', 0);
            }
            
            $taggedPubs = $taggedPubsQuery->inRandomOrder()->take(10)->get();
            $relatedPubs = $relatedPubs->merge($taggedPubs);
        }
        
        // Priority 2: If we don't have enough, add publications with same thematic area
        if ($relatedPubs->count() < 10 && $thematicAreaId) {
            $needed = 10 - $relatedPubs->count();
            $existingIds = $relatedPubs->pluck('id')->toArray();
            $existingIds[] = $data['publication']->id;
            
            $thematicPubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereNotIn('id', $existingIds)
                ->whereHas('sub_theme', function($q) use ($thematicAreaId) {
                    $q->where('thematic_area_id', $thematicAreaId);
                })
                ->with(['author', 'tags.tag', 'sub_theme']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $thematicPubsQuery->where('is_admin_only_access', 0);
            }
            
            $thematicPubs = $thematicPubsQuery->inRandomOrder()->take($needed)->get();
            $relatedPubs = $relatedPubs->merge($thematicPubs);
        }
        
        // Priority 3: If we still don't have enough, add publications with same sub-theme
        if ($relatedPubs->count() < 10 && $subThematicAreaId) {
            $needed = 10 - $relatedPubs->count();
            $existingIds = $relatedPubs->pluck('id')->toArray();
            $existingIds[] = $data['publication']->id;
            
            $subthemePubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereNotIn('id', $existingIds)
                ->where('sub_thematic_area_id', $subThematicAreaId)
                ->with(['author', 'tags.tag', 'sub_theme']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $subthemePubsQuery->where('is_admin_only_access', 0);
            }
            
            $subthemePubs = $subthemePubsQuery->inRandomOrder()->take($needed)->get();
            $relatedPubs = $relatedPubs->merge($subthemePubs);
        }
        
        // Limit to 10 and remove duplicates
        $data['related_publications'] = $relatedPubs->unique('id')->take(10);
      
        return view('publications.show',$data);
    }

    public function shortened(Request $request){

        $summary              = $this->publicationsRepo->find_shortened($request->id);
        $data['publication']  = $this->publicationsRepo->find($summary->resource_id);
        $data['abstract']     = $summary;

        if(!$data['publication'] || !$summary)
        abort(404);
      
        return view('publications.abstract',$data);
    }

    public function search(Request $request){
        // Basic input hardening for search
        $request->merge([
            'term' => is_string($request->term) ? strip_tags(trim($request->term)) : null,
        ]);
        $request->validate([
            'term' => 'nullable|string|max:255',
            'thematic_area_id' => 'nullable|integer',
            'subtheme' => 'nullable|integer',
            'author' => 'nullable|integer',
            'country_id' => 'nullable|integer',
        ]);

        $request['thematic_area_id'] = $request->theme ?? $request->thematic_area_id;
        $data['sub_themes'] = ($request->thematic_area_id) ? $this->publicationsRepo->get_subthemes($request) : [];

        $data['publications'] = $this->publicationsRepo->get($request);
        $data['search']       = (Object) $request->all();

        // Get latest publications for sidebar
        $latestRequest = clone $request;
        $latestRequest->merge(['rows' => 5]);
        $data['latestPublications'] = $this->publicationsRepo->get($latestRequest);
        
        // Get related publications for sidebar
        $relatedRequest = clone $request;
        $relatedRequest->merge(['rows' => 5]);
        $data['relatedPublications'] = $this->publicationsRepo->get($relatedRequest);
        
        // Get popular tags for sidebar - tags that have approved publications
        $tagIds = \DB::table('publication_tags')
            ->join('publication', 'publication_tags.publication_id', '=', 'publication.id')
            ->where('publication.is_active', 'Active')
            ->where('publication.is_approved', 1)
            ->distinct()
            ->pluck('publication_tags.tag_id');
        
        $data['tags'] = \App\Models\Tag::whereIn('id', $tagIds)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('publications.search',$data);
    }

    public function author_pubs(Request $request){

        $data['author']       = $this->authorsRepo->find($request->author);
        $data['publications'] = $this->publicationsRepo->get($request);

        return view('publications.author_pubs',$data);
    }

    public function subtheme_pubs(Request $request){

        $data['subtheme']     = $this->publicationsRepo->get_subtheme($request->subtheme);
        $data['publications'] = $this->publicationsRepo->get($request);

        return view('publications.subtheme_pubs',$data);
    }

    public function autocomplete(Request $request){

        $searches     = $this->publicationsRepo->get($request,true);
        return response()->json($searches);
    }

    public function add_favourite(Request $request){

        //logic here
        $this->publicationsRepo->add_favourite($request->id);
        return back();
    }

    public function remove_favourite(Request $request){

        $this->publicationsRepo->remove_favourite($request->id);
        return back();
    }

    public function comment(Request $request){
        
        $this->publicationsRepo->save_comment($request);
        return back();
    }

    public function request_content(Request $request){

        if($request->getMethod()=='POST'){
            // Validate and verify captcha
            $request->merge([
                'title' => is_string($request->title) ? strip_tags(trim($request->title)) : $request->title,
            ]);
            $val_rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'country_id' => 'nullable|integer',
                'email' => 'nullable|email',
            ];

            $messages = [];

            // Add reCAPTCHA validation if site key is configured
            $recaptchaSiteKey = config('recaptcha.api_site_key');
            if ($recaptchaSiteKey && !empty($recaptchaSiteKey)) {
                $val_rules['g-recaptcha-response'] = 'required';
                $messages['g-recaptcha-response.required'] = 'Please complete the CAPTCHA to proceed.';
            }

            $request->validate($val_rules, $messages);

            // Validate reCAPTCHA response if provided
            if ($recaptchaSiteKey && !empty($recaptchaSiteKey)) {
                if (!$request->filled('g-recaptcha-response')) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'Please complete the CAPTCHA to proceed.'
                    ])->withInput();
                }
                
                $recaptchaResponse = $request->input('g-recaptcha-response');
                if (!\Biscolab\ReCaptcha\Facades\ReCaptcha::validate($recaptchaResponse)) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.'
                    ])->withInput();
                }
            }

            $saved = $this->publicationsRepo->save_content_request($request);
            if($saved):
                $data = ['message'=>'Request submitted successfully','status'=>'success','data'=>$saved];
            else:
                $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
            endif;
        
            return back()->with($data);;
        }
            

        return view('publications.content_request');
    }

   
}
