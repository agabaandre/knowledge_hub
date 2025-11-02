<?php

namespace App\Http\Controllers;

use App\Repositories\PublicationsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use App\Models\AccessLevel;
class AccountController extends Controller
{
    private $publicationsRepo,$usersRepo;

    public function __construct( PublicationsRepository $publicationsRepo,UsersRepository $usersRepo)
    {
        $this->publicationsRepo       = $publicationsRepo;
        $this->usersRepo            = $usersRepo;
    }


    public function profile(Request $request){

        $user = current_user();
        $data['user'] = $user;
        // Preload selected preferences as an array of SubThemeticArea IDs
        // Avoid ambiguous column 'id' by qualifying the table name
        $data['preferences'] = [];
        if ($user) {
            $data['preferences'] = $user->preferences()->pluck('subtheme_id')->toArray();
        }
        $data['access_groups'] = AccessLevel::all();
        
        return view('account.profile',$data);
    }

    public function verifyAccount(Request $request){

        $verified = $this->usersRepo->verify_account($request);
        $message  = ($verified)?'Account verified and activated successfully! You can now login.':'Verification failed. Invalid or expired token. Please try again.';
        $alert_class  = ($verified)?'success':'danger';

        return redirect()->route('login')->with(['alert'=>$message,'alert_class'=>$alert_class]);
    }


    public function favourites(Request $request){

        $data['favourites'] = $this->publicationsRepo->favourites($request);
        
        // Get recommended content by preferences (10 max)
        $data['recommendedByPreferences'] = $this->publicationsRepo->recommendedByPreferences(auth()->user()->id, 10);
        
        // Get related content by favorite tags (10 max)
        $data['relatedByFavoriteTags'] = $this->publicationsRepo->relatedByFavoriteTags(auth()->user()->id, 10);
        
        return view('account.favourites',$data);
    }


    public function publications(Request $request){
        // Server-side DataTables JSON
        if ($request->ajax() && $request->input('datatable')) {
            $userId = auth()->id();
            $draw   = intval($request->input('draw'));
            $start  = intval($request->input('start', 0));
            $length = intval($request->input('length', 10));

            $base = \App\Models\Publication::query()
                ->with(['author'])
                ->where('user_id', $userId);

            $recordsTotal = (clone $base)->count();

            // Global search
            $search = $request->input('search.value');
            if ($search) {
                $base->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
                });
            }

            $recordsFiltered = (clone $base)->count();

            // Ordering (default: id desc)
            $orderColIndex = intval($request->input('order.0.column', 0));
            $orderDir      = $request->input('order.0.dir', 'desc');
            $columns       = ['id','title','description','is_approved','visits','created_at'];
            $orderCol      = $columns[$orderColIndex] ?? 'id';

            $rows = $base->orderBy($orderCol, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            $data = [];
            $index = $start + 1;
            foreach ($rows as $row) {
                $status = get_publication_state($row->is_approved, $row->is_rejected);
                $statusBadge = '<span class="badge '.($row->is_approved ? 'badge-success' : 'badge-secondary').'">'.$status.'</span>';
                $title = '<a href="'.e($row->publication).'" target="_blank">'.truncate($row->title, 50).'</a>';
                $desc  = truncate(html_to_text($row->description), 80);
                if (($row->is_rejected ?? 0) == 1 && !empty($row->rejected_reason)) {
                    $desc .= '<div class="mt-2 p-2" style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;"><small class="text-danger"><strong>Rejection reason:</strong> '.e($row->rejected_reason).'</small></div>';
                }
                
                // Get total views from monthly views table
                $totalViews = \App\Models\PublicationView::getTotalViews($row->id);
                
                // Build actions - only show edit/delete if not approved
                $isApproved = ($row->is_approved ?? 0) == 1;
                $actions = '<div class="btn-group btn-group-sm" role="group" aria-label="Actions">'
                    .'<a href="'.url('records/resource').'?id='.$row->id.'" class="btn btn-outline-secondary" target="_blank"><i class="fa fa-eye"></i> Preview</a>';
                
                // Only show edit and delete buttons if publication is not approved
                if (!$isApproved) {
                    $actions .= '<a href="'.route('account.publications.edit').'?ref='.$row->id.'" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Edit</a>'
                        .'<a href="javascript:void(0);" onclick="openDeleteModal('.$row->id.')" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</a>';
                }
                
                $actions .= '</div>';

                $data[] = [
                    $index++,
                    $title,
                    $desc,
                    $statusBadge,
                    '<span class="badge badge-info">'.number_format($totalViews).'</span>',
                    $row->created_at ? $row->created_at->format('Y-m-d H:i') : 'N/A',
                    $actions,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        // Get statistics for the user's publications
        $userId = auth()->id();
        $userPublications = \App\Models\Publication::where('user_id', $userId)->get();
        
        $stats = [
            'total' => $userPublications->count(),
            'approved' => $userPublications->where('is_approved', 1)->count(),
            'pending' => $userPublications->where('is_approved', 0)->where('is_rejected', 0)->count(),
            'rejected' => $userPublications->where('is_rejected', 1)->count(),
            'total_views' => 0,
        ];
        
        // Calculate total views across all publications
        foreach ($userPublications as $pub) {
            $stats['total_views'] += \App\Models\PublicationView::getTotalViews($pub->id);
        }
        
        // Get forum engagement statistics
        $stats['forum_posts'] = \App\Models\ForumEngagement::getTotalForumPosts($userId);
        $stats['forum_comments'] = \App\Models\ForumEngagement::getTotalForumComments($userId);
        $stats['forum_engagements'] = \App\Models\ForumEngagement::getTotalEngagements($userId);
        
        // Get communities the user belongs to
        $stats['communities'] = \App\Models\CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('is_approved', 1)
            ->with('community')
            ->get()
            ->pluck('community.community_name')
            ->filter()
            ->toArray();
        
        $data['stats'] = $stats;
        $data['publications'] = $this->publicationsRepo->my_publications($request);
        return view('account.mypublications', $data);
    }


    public function publish(Request $request){

        return view('account.create');
    }

    public function edit_publication(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->ref);
        return view('account.editpub',$data);
    }

    public function create_version(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        
        return view('account.create_version',$data);
    }


    public function submit_publication(Request $request){

        // Get minimum word count from settings (default 150 words = ~750 characters)
        $minWords = settings()->publication_min_words ?? 150;
        $minChars = $minWords * 5; // Approximate: 5 characters per word

        // Get required fields from settings
        $requiredFields = json_decode(settings()->publication_required_fields ?? '{}', true);
        if (empty($requiredFields)) {
            $requiredFields = [
                'title' => true,
                'description' => true,
                'associated_authors' => true,
                'tags' => true,
                'theme' => true,
                'sub_theme' => true,
                'data_category_id' => true,
            ];
        }

        // Check if user is admin using the same logic as the view (role name contains 'admin')
        $isAdmin = false;
        if (auth()->check()) {
            $role = \get_role(auth()->user()->id);
            $isAdmin = ($role && strpos(strtolower($role->name), 'admin') !== false);
        }

        $val_rules = [
            'title' => ($requiredFields['title'] ?? true) ? 'required|string|max:500' : 'nullable|string|max:500',
            'description' => ($requiredFields['description'] ?? true) ? 'required|string|min:' . $minChars : 'nullable|string|min:' . $minChars,
            'associated_authors' => ($requiredFields['associated_authors'] ?? true) ? 'required|string|max:500' : 'nullable|string|max:500',
            'author_affiliation' => 'required|string|max:500',
            'tags' => ($requiredFields['tags'] ?? true) ? 'required|array|min:1' : 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'theme' => ($requiredFields['theme'] ?? true) ? 'required' : 'nullable',
            'sub_theme' => ($requiredFields['sub_theme'] ?? true) ? 'required' : 'nullable',
            'data_category_id' => ($requiredFields['data_category_id'] ?? true) ? 'required' : 'nullable',
            'year_published' => ($requiredFields['year_published'] ?? false) ? 'required|integer|min:1900|max:' . date('Y') : 'nullable|integer|min:1900|max:' . date('Y'),
            // Author field: only required for admins OR if explicitly set in requiredFields
            // For non-admin users, author_id is automatically set from user's author_id
            'author' => ($isAdmin && ($requiredFields['author'] ?? false)) ? 'required' : 'nullable',
            'doi' => ($requiredFields['doi'] ?? false) ? 'required|string|max:255' : 'nullable|string|max:255',
            'issn' => ($requiredFields['issn'] ?? false) ? 'required|string|max:50' : 'nullable|string|max:50',
            'isbn' => ($requiredFields['isbn'] ?? false) ? 'required|string|max:50' : 'nullable|string|max:50',
            'license_id' => ($requiredFields['license_id'] ?? false) ? 'required|exists:licenses,id' : 'nullable|exists:licenses,id',
            'copyright_info' => ($requiredFields['copyright_info'] ?? false) ? 'required|string' : 'nullable|string',
            // Member States are optional when audience is global/All
            'countries' => 'nullable|array',
            'countries.*' => 'exists:country,id',
        ];

        $messages = [
            'data_category_id.required' => 'Please select a category for your resource.',
            'theme.required' => 'Please select a thematic area.',
            'sub_theme.required' => 'Please select a sub-theme.',
            'title.required' => 'A resource title is required. Please provide a clear, descriptive title.',
            'title.max' => 'The title cannot exceed 500 characters.',
            'description.required' => 'A description is required. Please describe your resource in detail.',
            'description.min' => 'The description must be at least ' . $minWords . ' words (approximately ' . $minChars . ' characters). Please provide more details about your resource.',
            'associated_authors.required' => 'Associated authors are required. Please list the authors or co-authors.',
            'associated_authors.max' => 'Associated authors cannot exceed 500 characters.',
            'author_affiliation.required' => 'Author affiliation/institution is required. Please enter the institution or organization of the authors.',
            'author_affiliation.max' => 'Author affiliation cannot exceed 500 characters.',
            'tags.required' => 'Please select at least one tag/health topic to help categorize your publication.',
            'tags.min' => 'Please select at least one tag/health topic.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
            'year_published.required' => 'Year published is required.',
            'author.required' => 'Source/Author is required.',
            'doi.required' => 'DOI is required.',
            'issn.required' => 'ISSN is required.',
            'isbn.required' => 'ISBN is required.',
            'license_id.required' => 'License is required.',
            'copyright_info.required' => 'Copyright information is required.',
            'countries.required' => 'Please select at least one member state.',
            'countries.array' => 'Please select at least one member state.',
            'countries.min' => 'Please select at least one member state.',
        ];

        // For non-admin users, ensure they have an author_id set
        if (!is_admin() && !auth()->user()->author_id) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.',
                    'alert_class' => 'danger'
                ], 422);
            }
            return back()->withErrors([
                'author' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.'
            ])->withInput();
        }

        // For link type, require URL
        if($request->upload_type == 'link'):
            $val_rules['link'] = 'required|url';
            $messages['link.required'] = 'Please provide the external link URL for your resource.';
            $messages['link.url'] = 'Please provide a valid URL (starting with http:// or https://).';
        endif;

        // Attachments are optional at submission time (user may supply a link instead)

        if($request->original_id):
            unset($val_rules['sub_theme']);
            unset($val_rules['title']);
            $request['file_type'] = 1;
        endif;
        
        if(!$request->is_active)
         $request['is_active']='In-Active';

        if($request->id):
            unset($val_rules['cover']);
        endif;

        $request->validate($val_rules, $messages);

        // Default: show disclaimer if checkbox not sent
        if (!$request->has('show_disclaimer')) {
            $request['show_disclaimer'] = 1;
        }

        $saved   = $this->publicationsRepo->save($request);
        $message = ($saved)?'Publication saved successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = ($saved)?'success':'error';

        if($request->ajax())
           return response()->json($data, $saved ? 200 : 400);

        return redirect()->route('account.publications')->with($data);

    }

    public function create_summary(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        return view('account.create_summary',$data);
    }


    public function submit_summary(Request $request){

        $val_rules = [
            //'file_type'=>'required',
            'summary'  =>'required',
            'title'    =>'required'
        ];

        $request->validate($val_rules);

        $saved   = $this->publicationsRepo->save_summary($request);
        $message = ($saved)?'Resource summary submitted successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        if($request->ajax())
           return response($data);

        return redirect(url('records/resource?id='.$request->original_id))->with($data);

    }

    public function delete_publication(Request $request)
    {
        $this->publicationsRepo->delete($request->id);
    }



}
