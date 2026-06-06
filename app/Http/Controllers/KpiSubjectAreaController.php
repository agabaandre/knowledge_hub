<?php

namespace App\Http\Controllers;

use App\Repositories\SubjectAreasRepository;
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
}
