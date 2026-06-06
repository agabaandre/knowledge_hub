<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;
use App\Support\PublicationSubmissionValidation;
use Illuminate\Validation\ValidationException;

class ResourcesController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo,
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }

    public function index(Request $request){
        if ($request->ajax() && $request->boolean('datatable')) {
            return response()->json($this->publicationsRepo->adminApprovedDatatable($request));
        }

        $data['search'] = (object) $request->all();
        
        // Count pending publications for notification bell
        $data['pending_publications_count'] = \App\Models\Publication::where('is_approved', 0)
            ->where('is_rejected', 0)
            ->count();
        
        return view('admin.publications.index', $data);
    }

    
    public function pending(Request $request){
        $request['is_admin']  = 1;
        $request['rows'] = $request->rows ?? 20; // Set pagination to 20 per page
        $data['publications'] = $this->publicationsRepo->get($request,false, false,true);
        $data['search']       = (Object) $request->all();
        return view('admin.publications.pending',$data);
    }

    public function create(Request $request){
        $data['publication'] = null;
        $data['title']       = 'New Public Health Resource';
        return view('admin.publications.create', $data);
    }

    public function edit(Request $request){
        $publication = $this->publicationsRepo->find($request->id);
        $data['publication'] = $publication;
        $data['title']       = 'Edit: ' . ($publication->title ?? 'Resource');
        return view('admin.publications.create', $data);
    }

    public function details(Request $request){

        $publication          =  $this->publicationsRepo->find($request->id);
        $data['publication']  = $publication;
        return view('admin.publications.details',$data);
    }


    public function summaries(Request $request){

        $data['summaries']        =  $this->publicationsRepo->get_summaries($request);
        return view('admin.publications.summaries',$data);
    }

    public function summary(Request $request){

        $summary          =  $this->publicationsRepo->find_summary($request);
        $data['summary']  = $summary;
        return view('admin.publications.summary',$data);
    }

    public function store(Request $request){
        if (is_admin() && !auth()->user()->author_id && empty($request->author)) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Please select a Corporate Source or Member State (author).', 'status' => 'failure'], 422);
            }
            return back()->with(['message' => 'Please select a Corporate Source or Member State (author).', 'status' => 'failure']);
        }
        try {
            PublicationSubmissionValidation::assertAttachmentFilesAllowed($request);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Invalid attachment file type.',
                    'status' => 'failure',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }

        $saved = $this->publicationsRepo->save($request);

        if($saved):
            $data = ['message'=>'Resource saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back(200)->with($data);
    }

    public function approval(Request $request){

        $saved   = $this->publicationsRepo->change_approval_status($request);

        if($saved):
            $data = ['message'=>'Resource updated successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        return back()->with($data);
    }

    /**
     * Bulk approve or reject pending publications (from pending page).
     */
    public function bulkApproval(Request $request)
    {
        $ids = $request->input('publication_ids', []);
        $action = $request->input('action');
        $reason = $request->input('rejected_reason', '');

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No publications selected.');
        }
        if (!in_array($action, ['approve', 'reject'], true)) {
            return back()->with('error', 'Invalid action.');
        }

        $count = 0;
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }
            $req = new Request([
                'id' => $id,
                'approved' => $action === 'approve' ? 1 : 0,
                'rejected' => $action === 'reject' ? 1 : 0,
                'rejected_reason' => $reason,
                'is_summary' => 0,
            ]);
            $saved = $this->publicationsRepo->change_approval_status($req);
            if ($saved) {
                $count++;
            }
        }

        $message = $action === 'approve'
            ? "{$count} publication(s) approved."
            : "{$count} publication(s) rejected.";
        return back()->with('success', $message);
    }

    public function summary_approval(Request $request){

        $saved   = $this->publicationsRepo->change_approval_status($request);

        if($saved):
            $data = ['message'=>'Resource updated successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        return back()->with($data);
    }
    public function moderate(Request $request){

        $data['publications'] = $this->publicationsRepo->with_pending_comments($request);
        $data['search']       = (Object) $request->all();
        return view('admin.publications.moderate',$data);
    }

    public function destroy(Request $request){

        return $this->publicationsRepo->delete($request->id);
    }

    public function approve_comment(Request $request){
        return $this->publicationsRepo->approve_comment($request->id);
    }


    public function reject_comment(Request $request){
        return $this->publicationsRepo->reject_comment($request->id);
    }

    public function import(Request $request){

        $saved = $this->publicationsRepo->import($request);

        if($saved):
            $data = ['message'=>'Resources imported successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        return back()->with($data);
    }

    public function import_template(Request $request){
        return response()->download(public_path('import-template.xlsx'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('selected_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No publications selected.');
        }
        switch ($action) {
            case 'inactive':
                $this->publicationsRepo->bulkInactive($ids);
                return back()->with('success', 'Selected publications unpublished.');
            case 'delete':
                $this->publicationsRepo->bulkDelete($ids);
                return back()->with('success', 'Selected publications deleted.');
            case 'featured':
                $this->publicationsRepo->bulkFeatured($ids);
                return back()->with('success', 'Selected publications marked as featured.');
            default:
                return back()->with('error', 'Invalid action.');
        }
    }

}
