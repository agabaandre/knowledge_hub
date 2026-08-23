<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\CommunityOfPractice;
use App\Models\ContentRequest;
use App\Models\ContentRequestReferralMessage;
use App\Models\ContentRequestReferralTarget;
use App\Models\User;
use App\Jobs\SendContentRequestForumAiSummaryJob;
use App\Services\ContentRequestReferralForumService;
use App\Services\ContentRequestReferralNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContentRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->boolean('datatable')) {
            return response()->json($this->contentRequestsDatatable($request));
        }

        $referUsers = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('name')
            ->limit(5000)
            ->get(['id', 'name', 'email']);

        $referCommunities = CommunityOfPractice::query()
            ->orderBy('community_name')
            ->get(['id', 'community_name']);

        return view('admin.content_requests.index', compact('referUsers', 'referCommunities'));
    }

    private function buildContentRequestsQuery(Request $request)
    {
        $query = ContentRequest::with([
            'country',
            'processedBy',
            'referredToUser',
            'referredToCommunity',
            'referralTargets.user',
            'referralTargets.community',
        ]);

        if ($request->filled('status')) {
            if ($request->status === 'processed') {
                $query->whereNotNull('processed_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('processed_at');
            } elseif ($request->status === 'referred') {
                $query->whereNotNull('referral_type')->whereNotNull('referred_at');
            }
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function contentRequestsDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 10)), 100);

        $base = $this->buildContentRequestsQuery($request);
        $recordsTotal = ContentRequest::query()->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 6);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [1 => 'subject', 5 => 'email', 6 => 'created_at'];
        $orderCol = $orderMap[$orderColIndex] ?? 'created_at';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        $canManage = auth()->user() && auth()->user()->can('manage_content_requests');

        $data = [];
        $index = $start + 1;
        foreach ($rows as $row) {
            $statusHtml = $this->contentRequestStatusHtml($row);
            $dateHtml = '<small>'.$row->created_at->format('M d, Y').'</small>';
            if ($row->processed_at) {
                $dateHtml .= '<br><small class="text-muted">Processed: '.$row->processed_at->format('M d, Y').'</small>';
            }

            $data[] = [
                'index' => $index++,
                'subject' => '<strong>'.e($row->subject).'</strong>',
                'description' => $this->contentRequestDescriptionCell($row->description),
                'country' => e($row->country->name ?? 'N/A'),
                'email' => e($row->email ?? 'N/A'),
                'status' => $statusHtml,
                'date' => $dateHtml,
                'actions' => $this->contentRequestActionsHtml($row, $canManage),
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    private function contentRequestStatusHtml(ContentRequest $request): string
    {
        $html = '';
        if ($request->isProcessed()) {
            $html .= '<span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i>Processed</span>';
            $html .= '<br><small class="text-muted">Method: '.e($request->processingMethodLabel()).'</small>';
            if ($request->processedBy) {
                $html .= '<br><small class="text-muted">By: '.e($request->processedBy->name).'</small>';
            }
        } else {
            $html .= '<span class="badge badge-warning"><i class="fa fa-clock mr-1"></i>Pending</span>';
        }

        if ($request->isReferred()) {
            $html .= '<br><span class="badge badge-info mt-1"><i class="fa fa-share mr-1"></i>Referred</span>';
            $rt = $request->referralTargets;
            $nUsers = $rt->whereNotNull('user_id')->count();
            $nComms = $rt->whereNotNull('community_of_practice_id')->count();
            if ($nUsers + $nComms > 0) {
                $html .= '<br><small class="text-muted">'.$nUsers.' user(s), '.$nComms.' comm.</small>';
            }
        }

        return $html;
    }

    private function contentRequestDescriptionCell(?string $description): string
    {
        $safe = sanitize_rich_text_for_display($description ?? '');
        if ($safe === '') {
            return '<span class="text-muted">—</span>';
        }

        return '<div class="cr-description-cell">'.$safe.'</div>';
    }

    private function contentRequestActionsHtml(ContentRequest $request, bool $canManage): string
    {
        $descriptionB64 = base64_encode(sanitize_rich_text_for_display($request->description ?? ''));
        $html = '<div class="cr-actions-inline">';

        if (!$request->isProcessed()) {
            $html .= '<button type="button" class="btn btn-success btn-sm process-request-btn" data-id="'.$request->id.'" data-subject="'.e($request->subject).'" data-description-b64="'.e($descriptionB64).'" title="Process Request"><i class="fa fa-check"></i><span class="d-none d-xl-inline ml-1">Process</span></button>';
        } else {
            $html .= '<button type="button" class="btn btn-info btn-sm view-processed-btn" data-id="'.$request->id.'" data-subject="'.e($request->subject).'" data-links="'.e($request->content_links ?? '').'" data-comments="'.e($request->admin_comments ?? '').'" data-processed-by="'.e($request->processedBy->name ?? 'Unknown').'" data-processed-at="'.e($request->processed_at ? $request->processed_at->format('M d, Y H:i') : '').'" data-process-method="'.e($request->processingMethodLabel()).'" title="View Processed Details"><i class="fa fa-eye"></i><span class="d-none d-xl-inline ml-1">View</span></button>';
        }

        if ($canManage) {
            if (!$request->isReferred()) {
                $html .= '<button type="button" class="btn btn-primary btn-sm refer-request-btn" data-id="'.$request->id.'" data-subject="'.e($request->subject).'" title="Refer to user or community"><i class="fa fa-share"></i><span class="d-none d-xl-inline ml-1">Refer</span></button>';
            } else {
                $html .= '<a href="'.e($request->discussionUrl()).'" class="btn btn-secondary btn-sm" title="Open discussion"><i class="fa fa-comments"></i><span class="d-none d-xl-inline ml-1">Discuss</span></a>';
                if ($request->trackUrl() !== '') {
                    $html .= '<button type="button" class="btn btn-outline-secondary btn-sm copy-track-btn" data-url="'.e($request->trackUrl()).'" title="Copy requester tracking link"><i class="fa fa-link"></i></button>';
                }
            }
        }

        $html .= '<a href="'.route('admin.content-requests.edit', $request->id).'" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';
        $html .= '<form action="'.route('admin.content-requests.destroy', $request->id).'" method="POST" class="cr-actions-inline__form" onsubmit="return confirm(\'Are you sure you want to delete this content request?\');">'
            .csrf_field().method_field('DELETE')
            .'<button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button></form>';
        $html .= '</div>';

        return $html;
    }

    public function create()
    {
        // Show the form to create a new content request
        return view('admin.content_requests.create');
    }

    public function store(Request $request)
    {
        // Validate and store the new content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:country,id',
            'email' => 'nullable|email',
        ]);

        ContentRequest::create($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request created successfully.');
    }

    public function edit($id)
    {
        // Fetch the content request for editing
        $contentRequest = ContentRequest::findOrFail($id);
        return view('admin.content_requests.edit', compact('contentRequest'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update the content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:country,id',
            'email' => 'nullable|email',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->update($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the content request
        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->delete();

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request deleted successfully.');
    }

    /**
     * Process a content request - mark as processed and send email to requester
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'content_links' => 'required|string|min:10',
            'admin_comments' => 'nullable|string|max:1000',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);

        // Update the content request with processing information
        $contentRequest->update([
            'processed_at' => now(),
            'processed_by' => Auth::id(),
            'content_links' => $request->content_links,
            'admin_comments' => $request->admin_comments,
        ]);

        // Send email to requester
        if ($contentRequest->email) {
            $emailData = [
                'to' => $contentRequest->email,
                'subject' => 'Your Content Request Has Been Processed - ' . $contentRequest->subject,
                'title' => 'Content Request Processed',
                'body' => view('emails.content_request_processed', [
                    'contentRequest' => $contentRequest,
                    'contentLinks' => $request->content_links,
                    'adminComments' => $request->admin_comments,
                ])->render(),
            ];

            SendMailJob::dispatch($emailData)->onQueue('default');
        }

        return redirect()->route('admin.content-requests.index')
            ->with('success', 'Content request processed successfully and email sent to requester.');
    }

    /**
     * Refer a request to a hub user or a community for discussion; notifies requester and assignees.
     */
    public function refer(Request $request, $id)
    {
        $request->validate([
            'referred_user_ids' => 'nullable|array',
            'referred_user_ids.*' => 'integer|exists:users,id',
            'referred_community_ids' => 'nullable|array',
            'referred_community_ids.*' => 'integer|exists:community_of_practices,id',
            'referral_notes' => 'nullable|string|max:5000',
        ]);

        $userIds = array_values(array_unique(array_filter(array_map('intval', $request->input('referred_user_ids', [])))));
        $communityIds = array_values(array_unique(array_filter(array_map('intval', $request->input('referred_community_ids', [])))));

        if (count($userIds) + count($communityIds) < 2) {
            return redirect()->route('admin.content-requests.index')
                ->with('error', 'Select at least two assignees in total (hub users and/or communities).');
        }

        $contentRequest = ContentRequest::findOrFail($id);

        if ($contentRequest->isReferred()) {
            return redirect()->route('admin.content-requests.index')
                ->with('error', 'This request has already been referred. Open the hub discussion to add messages.');
        }

        $referralType = count($communityIds) > 0 && count($userIds) > 0
            ? 'mixed'
            : (count($communityIds) > 0 ? 'community' : 'user');

        $token = $contentRequest->requestor_track_token ?: Str::random(48);

        $contentRequest->update([
            'referral_type' => $referralType,
            'referred_to_user_id' => $userIds[0] ?? null,
            'referred_to_community_id' => $communityIds[0] ?? null,
            'referred_at' => now(),
            'referred_by' => Auth::id(),
            'referral_notes' => $request->referral_notes,
            'requestor_track_token' => $token,
            'referral_forum_id' => null,
        ]);

        $contentRequest->refresh();

        $firstForumId = null;

        foreach ($communityIds as $cid) {
            $target = ContentRequestReferralTarget::create([
                'content_request_id' => $contentRequest->id,
                'user_id' => null,
                'community_of_practice_id' => $cid,
                'referral_forum_id' => null,
            ]);

            $forum = ContentRequestReferralForumService::createForCommunityReferral(
                $contentRequest->fresh(),
                $cid,
                (int) Auth::id()
            );
            if ($forum) {
                $target->update(['referral_forum_id' => $forum->id]);
                if ($firstForumId === null) {
                    $firstForumId = $forum->id;
                }
            }
        }

        foreach ($userIds as $uid) {
            ContentRequestReferralTarget::create([
                'content_request_id' => $contentRequest->id,
                'user_id' => $uid,
                'community_of_practice_id' => null,
                'referral_forum_id' => null,
            ]);
        }

        if ($firstForumId !== null) {
            $contentRequest->update(['referral_forum_id' => $firstForumId]);
        }

        $contentRequest->refresh();

        $forumLines = [];
        foreach ($contentRequest->referralTargets()->whereNotNull('referral_forum_id')->get() as $t) {
            $forumLines[] = ContentRequestReferralForumService::forumThreadUrl((int) $t->referral_forum_id);
        }
        $forumLines = array_values(array_unique(array_filter($forumLines)));

        $intro = trim((string) $request->referral_notes) !== ''
            ? strip_tags($request->referral_notes)
            : 'This request has been referred on the Knowledge Hub for follow-up and discussion.';
        if (count($forumLines) > 0) {
            $intro .= "\n\nCommunity forum thread(s):\n".implode("\n", $forumLines);
        }

        ContentRequestReferralMessage::create([
            'content_request_id' => $contentRequest->id,
            'user_id' => Auth::id(),
            'posted_via_track' => false,
            'body' => $intro,
        ]);

        ContentRequestReferralNotifier::notifyReferralCreated(
            $contentRequest->fresh([
                'referredToUser',
                'referredToCommunity',
                'referredByUser',
                'country',
                'referralTargets.user',
                'referralTargets.community',
            ])
        );

        if ($contentRequest->email) {
            foreach ($contentRequest->referralForumIds()->values()->all() as $i => $fid) {
                SendContentRequestForumAiSummaryJob::dispatch($contentRequest->id, (int) $fid)
                    ->delay(now()->addSeconds(20 + ($i * 15)));
            }
        }

        return redirect()->route('admin.content-requests.index')
            ->with('success', 'Request referred. The requester and assignee(s) have been notified by email.');
    }
}
