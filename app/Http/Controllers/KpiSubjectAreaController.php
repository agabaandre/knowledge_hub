<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessKpiOwidActionJob;
use App\Repositories\SubjectAreasRepository;
use App\Services\Kpi\KpiDeduplicationService;
use Illuminate\Http\Request;

class KpiSubjectAreaController extends Controller
{
    public function __construct(private SubjectAreasRepository $subjectAreasRepo)
    {
    }

    public function index(Request $request)
    {
        $data['search'] = (object) $request->all();
        $data['subject_areas'] = $this->subjectAreasRepo->get($request);
        $data['kpi_stats'] = kpi_admin_stats();
        $data['subject_area_duplicate_groups'] = app(KpiDeduplicationService::class)->findSubjectAreaDuplicateGroups();

        return view('admin.kpi.subject_areas.index', $data);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'owid_topic' => 'nullable|string|max:191',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        $this->subjectAreasRepo->save($request);

        return back()->with('alert-success', $request->id ? 'Subject area updated.' : 'Subject area created.');
    }

    public function get(Request $request)
    {
        $area = $this->subjectAreasRepo->find((int) $request->id);
        if (! $area) {
            return response()->json(['success' => false], 404);
        }

        return response()->json(['success' => true, 'subject_area' => $area]);
    }

    public function destroy(Request $request)
    {
        if (! $this->subjectAreasRepo->delete((int) $request->id)) {
            return back()->with('alert-danger', 'Unable to delete subject area. Remove linked indicators first.');
        }

        return back()->with('alert-success', 'Subject area deleted.');
    }

    public function dedupeAuto(Request $request)
    {
        $runs = app(\App\Services\Kpi\KpiSyncRunService::class);
        $run = $runs->create('dedupe_subject_areas', [], auth()->id());
        ProcessKpiOwidActionJob::dispatch($run->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'run_id' => $run->id,
                'message' => 'Merging duplicate subject areas…',
            ]);
        }

        return back()
            ->with('kpi_sync_run_id', $run->id)
            ->with('alert-info', 'Merging duplicate subject areas… Track progress below.');
    }

    public function merge(Request $request, KpiDeduplicationService $dedupe)
    {
        $request->validate([
            'keep_id' => 'required|integer|min:1',
            'duplicate_ids' => 'required|array|min:1',
            'duplicate_ids.*' => 'integer|min:1',
        ]);

        $result = $dedupe->mergeSubjectAreas((int) $request->keep_id, $request->duplicate_ids);
        $message = sprintf(
            'Merged %d duplicate subject area(s) and moved %d linked indicators.',
            $result['removed'],
            $result['moved_indicators']
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'result' => $result]);
        }

        return back()->with('alert-success', $message);
    }
}
