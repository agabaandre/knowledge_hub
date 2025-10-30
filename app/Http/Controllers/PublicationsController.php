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
        
        $request->merge(['thematic_area_id' => $data['publication']->thematic_area_id]);
        // Get related publications with the same thematic_area_id
        $data['related_publications'] = $this->publicationsRepo->get($request);
      
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

            if (config('recaptcha.sitekey')) {
                $val_rules['g-recaptcha-response'] = 'required';
            }

            $messages = [
                'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
            ];

            $request->validate($val_rules, $messages);

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
