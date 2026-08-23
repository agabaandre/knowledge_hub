<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FederatedContentItem;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\PublicationsRepository;
use App\Services\ApprovalInboxService;
use App\Support\ContentModeration;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ApprovalsController extends Controller
{
    public function __construct(
        private ApprovalInboxService $inbox,
        private PublicationsRepository $publicationsRepo,
        private ForumsRepository $forumsRepo,
        private CommsOfPracticeRepository $commsOfPracticeRepo,
    ) {
    }

    public function index(Request $request)
    {
        $type = (string) $request->input('type', 'all');
        if ($type !== 'all' && ! in_array($type, ApprovalInboxService::TYPES, true)) {
            $type = 'all';
        }

        $all = $this->inbox->pendingItems($type === 'all' ? null : $type);
        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $needle = mb_strtolower($q);
            $all = $all->filter(function (array $item) use ($needle) {
                $haystack = mb_strtolower(
                    $item['title'].' '.$item['subtitle'].' '.$item['submitted_by'].' '.$item['type_label']
                );

                return str_contains($haystack, $needle);
            })->values();
        }

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 25;
        $items = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.approvals.index', [
            'items' => $items,
            'counts' => $this->inbox->counts(),
            'currentType' => $type,
            'q' => $q,
        ]);
    }

    public function review(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'filter_type' => 'nullable|in:all,publication,forum,cop_participant,federated',
            'id' => 'nullable|integer',
            'keys' => 'nullable|array',
            'keys.*' => 'string',
            'rejected_reason' => 'nullable|string|max:5000',
        ]);

        $targets = $this->targetsFromRequest($data);
        if ($targets->isEmpty()) {
            return back()->with('alert-danger', 'Select at least one pending item.');
        }

        $done = 0;
        foreach ($targets as $target) {
            $this->applyDecision($target['type'], $target['id'], $data['action'], (string) ($data['rejected_reason'] ?? ''));
            $done++;
        }

        $verb = $data['action'] === 'approve' ? 'approved' : 'rejected';

        $filterType = $request->input('filter_type', 'all');
        if ($filterType !== 'all' && ! in_array($filterType, ApprovalInboxService::TYPES, true)) {
            $filterType = 'all';
        }

        return redirect()
            ->route('admin.approvals.index', array_filter([
                'type' => $filterType === 'all' ? null : $filterType,
                'q' => $request->input('q') ?: null,
            ]))
            ->with('alert-success', $done.' item(s) '.$verb.'.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return \Illuminate\Support\Collection<int, array{type:string,id:int}>
     */
    protected function targetsFromRequest(array $data)
    {
        $targets = collect();
        if (! empty($data['type']) && ! empty($data['id'])) {
            $targets->push(['type' => $data['type'], 'id' => (int) $data['id']]);
        }
        foreach ($data['keys'] ?? [] as $key) {
            if (! is_string($key) || ! str_contains($key, ':')) {
                continue;
            }
            [$type, $id] = explode(':', $key, 2);
            if (in_array($type, ApprovalInboxService::TYPES, true) && (int) $id > 0) {
                $targets->push(['type' => $type, 'id' => (int) $id]);
            }
        }

        return $targets->unique(fn ($row) => $row['type'].':'.$row['id'])->values();
    }

    protected function applyDecision(string $type, int $id, string $action, string $reason): void
    {
        if ($type === 'publication') {
            ContentModeration::ensureCanModeratePublications();
            $req = Request::create('/', 'POST', [
                'id' => $id,
                'approved' => $action === 'approve' ? 1 : 0,
                'rejected' => $action === 'reject' ? 1 : 0,
                'rejected_reason' => $reason,
                'is_summary' => 0,
            ]);
            $this->publicationsRepo->change_approval_status($req);

            return;
        }

        if ($type === 'forum') {
            ContentModeration::ensureCanModerateForums();
            if ($action === 'approve') {
                $this->forumsRepo->approve($id);
            } else {
                if (strlen(trim($reason)) < 10) {
                    throw ValidationException::withMessages([
                        'rejected_reason' => 'A rejection reason of at least 10 characters is required for forums.',
                    ]);
                }
                $this->forumsRepo->reject($id, $reason);
            }

            return;
        }

        if ($type === 'cop_participant') {
            ContentModeration::ensureCanModerateCopParticipants();
            $this->commsOfPracticeRepo->updateMemberStatus($id, $action);

            return;
        }

        if ($type === 'federated' && Schema::hasTable('federated_content_items')) {
            FederatedContentItem::query()
                ->where('id', $id)
                ->pendingReview()
                ->update([
                    'central_approved' => $action === 'approve',
                    'central_rejected' => $action === 'reject',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
        }
    }
}
