<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessKpiOwidActionJob;
use App\Models\Kpi;
use App\Models\KpiSyncRun;
use App\Models\Setting;
use App\Services\Kpi\KpiDeduplicationService;
use App\Services\Kpi\KpiSyncRunService;
use App\Services\Owid\OwidIndicatorSyncService;
use Illuminate\Http\Request;
use App\Repositories\IndicatorsRepository;
use Illuminate\Support\Facades\Schema;


class KpiController extends Controller
{
    private $indicatorsRepo;

    public function __construct(IndicatorsRepository $indicatorsRepo)
    {
        $this->indicatorsRepo = $indicatorsRepo;
    }

    public function index(Request $request)
    {
        $request['rows'] = $request->rows ?? 20;
        $data['search'] = (object) $request->all();
        $data['indicators'] = $this->indicatorsRepo->get($request);
        $data['subject_areas'] = $this->indicatorsRepo->get_subject_areas();
        $data['kpi_stats'] = kpi_admin_stats();
        $data['indicator_duplicate_groups'] = app(KpiDeduplicationService::class)->findIndicatorDuplicateGroups();

        return view('admin.kpi.index', $data);
    }

    public function save(Request $request)
    {
        $this->indicatorsRepo->save($request);

        return back()->with('alert-success', $request->id ? 'Indicator updated successfully.' : 'Indicator created successfully.');
    }

    public function save_data(Request $request)
    {
        $result = $this->indicatorsRepo->save_data($request);

        if ($result['count'] > 0) {
            $message = $result['count'].' record(s) saved successfully.';
            if (! empty($result['errors'])) {
                $message .= ' However, '.count($result['errors']).' error(s) occurred: '.implode('; ', $result['errors']);
            }

            return back()->with('alert-success', $message);
        }

        return back()->with('alert-danger', 'No records were saved. Please ensure all required fields are filled.');
    }

    public function data(Request $request)
    {
        $request['rows'] = $request->rows ?? 20;
        $data['search'] = (object) $request->all();
        $data['kpis'] = $this->indicatorsRepo->get_kpis();
        $data['countries'] = \App\Models\Country::where('national', 'National')->get();
        $data['kpi_data'] = $this->indicatorsRepo->get_kpi_data($request);
        $data['kpi_stats'] = kpi_admin_stats();

        return view('admin.kpi.data', $data);
    }

    public function get_data(Request $request)
    {
        $record = $this->indicatorsRepo->find_data($request->kpi_id, $request->country_id, $request->period);

        if ($record) {
            $periodStr = $record->period ?? '';
            $year = '';
            $month = '';
            if ($periodStr && strlen($periodStr) >= 7) {
                $parts = explode('-', $periodStr);
                if (count($parts) >= 2) {
                    $year = $parts[0];
                    $month = ltrim($parts[1], '0');
                    if (empty($month)) {
                        $month = '0';
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kpi_id' => $record->kpi_id,
                    'country_id' => $record->country_id,
                    'value' => $record->value,
                    'period' => $record->period,
                    'year' => $year,
                    'month' => $month,
                    'old_kpi_id' => $record->kpi_id,
                    'old_country_id' => $record->country_id,
                    'old_period' => $record->period,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    }

    public function update_data(Request $request)
    {
        $result = $this->indicatorsRepo->update_data($request);

        if ($result['success']) {
            $message = 'Indicator data updated successfully.';
            if (isset($result['count']) && $result['count'] > 1) {
                $message .= ' ('.$result['count'].' records updated)';
            }

            return back()->with('alert-success', $message);
        }

        return back()->with('alert-danger', $result['message'] ?? 'Failed to update indicator data.');
    }

    public function destroy_data(Request $request)
    {
        $deleted = $this->indicatorsRepo->delete_data($request->kpi_id, $request->country_id, $request->period);

        return back()->with(
            $deleted ? 'alert-success' : 'alert-danger',
            $deleted ? 'Indicator data deleted successfully.' : 'Failed to delete indicator data.'
        );
    }

    public function get(Request $request)
    {
        $indicator = $this->indicatorsRepo->find($request->id);

        if ($indicator) {
            return response()->json(['success' => true, 'indicator' => $indicator]);
        }

        return response()->json(['success' => false, 'message' => 'Indicator not found'], 404);
    }

    public function destroy(Request $request)
    {
        $this->indicatorsRepo->delete($request->id);

        return true;
    }

    public function saveSettings(Request $request)
    {
        if (! Schema::hasTable('setting')) {
            return back()->with('alert-danger', 'Settings table is not available.');
        }

        $settings = Setting::query()->where('status', 'active')->first()
            ?? Setting::query()->first();

        if (! $settings) {
            return back()->with('alert-danger', 'No active site settings row found.');
        }

        if (Schema::hasColumn('setting', 'kpi_owid_auto_fetch_enabled')) {
            $settings->kpi_owid_auto_fetch_enabled = (bool) $request->boolean('kpi_owid_auto_fetch_enabled');
        }
        if (Schema::hasColumn('setting', 'kpi_manual_data_only')) {
            $settings->kpi_manual_data_only = (bool) $request->boolean('kpi_manual_data_only');
        }

        $settings->save();

        return back()->with('alert-success', 'KPI indicator settings saved.');
    }

    public function taskStatus(int $id, KpiSyncRunService $runs)
    {
        $run = KpiSyncRun::query()->findOrFail($id);

        return response()->json($runs->toStatusArray($run));
    }

    public function discoverOwid(Request $request)
    {
        if (kpi_manual_data_only()) {
            return $this->rejectOwidImport($request);
        }

        return $this->queueTask($request, 'discover', [
            'subject_area_id' => $request->filled('subject_area_id') ? (int) $request->subject_area_id : null,
        ], 'Fetching new indicators from Our World in Data…');
    }

    public function syncOwid(Request $request)
    {
        if (kpi_manual_data_only()) {
            return $this->rejectOwidImport($request);
        }

        return $this->queueTask($request, 'sync', [
            'kpi_id' => $request->filled('kpi_id') ? (int) $request->kpi_id : null,
            'published_only' => (bool) $request->boolean('published_only'),
        ], 'Refreshing indicator country values…');
    }

    public function approve(Request $request)
    {
        if (kpi_manual_data_only()) {
            $kpi = Kpi::query()->findOrFail((int) $request->id);
            $kpi->status = 'published';
            $kpi->approved_by = auth()->id();
            $kpi->approved_at = now();
            $kpi->recalled_at = null;
            $kpi->save();

            return back()->with('alert-success', 'Indicator published. Add country values manually under Country values.');
        }

        return $this->queueTask($request, 'approve', [
            'kpi_id' => (int) $request->id,
            'user_id' => auth()->id(),
            'narrations' => true,
        ], 'Publishing indicator and syncing country data…');
    }

    public function recall(Request $request, OwidIndicatorSyncService $sync)
    {
        $kpi = Kpi::query()->findOrFail((int) $request->id);
        $sync->recall($kpi);

        return back()->with('alert-success', 'Indicator recalled and hidden from public country pages.');
    }

    public function approveDefaults(Request $request)
    {
        if (kpi_manual_data_only()) {
            return $this->rejectOwidImport($request);
        }

        return $this->queueTask($request, 'approve_defaults', [
            'user_id' => auth()->id(),
            'narrations' => (bool) $request->boolean('narrations'),
        ], 'Publishing recommended indicators…');
    }

    public function freshFetch(Request $request)
    {
        if (kpi_manual_data_only()) {
            return $this->rejectOwidImport($request);
        }

        return $this->queueTask($request, 'fresh_fetch', [], 'Running full Our World in Data refresh…');
    }

    public function generateNarrations(Request $request)
    {
        return $this->queueTask($request, 'generate_narrations', [], 'Queueing AI country summaries…');
    }

    public function syncOne(Request $request)
    {
        if (kpi_manual_data_only()) {
            return $this->rejectOwidImport($request);
        }

        return $this->queueTask($request, 'sync_one', [
            'kpi_id' => (int) $request->id,
        ], 'Refreshing indicator values…');
    }

    public function duplicateScan(Request $request, KpiDeduplicationService $dedupe)
    {
        return response()->json([
            'success' => true,
            'indicator_groups' => $dedupe->findIndicatorDuplicateGroups(),
            'subject_area_groups' => $dedupe->findSubjectAreaDuplicateGroups(),
            'stats' => kpi_admin_stats(),
        ]);
    }

    public function dedupeIndicatorsAuto(Request $request, KpiDeduplicationService $dedupe)
    {
        $result = $dedupe->autoDedupeIndicators();
        $message = sprintf(
            'Merged duplicate indicators in %d group(s); removed %d duplicate record(s).',
            $result['merged'],
            $result['removed']
        );
        if (! empty($result['errors'])) {
            $message .= ' Notes: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sync' => true,
                'message' => $message,
                'result' => $result,
            ]);
        }

        return back()->with('alert-success', $message);
    }

    public function mergeIndicators(Request $request, KpiDeduplicationService $dedupe)
    {
        $request->validate([
            'keep_id' => 'required|integer|min:1',
            'duplicate_ids' => 'required|array|min:1',
            'duplicate_ids.*' => 'integer|min:1',
        ]);

        $result = $dedupe->mergeIndicators((int) $request->keep_id, $request->duplicate_ids);
        $message = sprintf(
            'Merged %d duplicate indicator(s). Moved %d country values and %d narrations.',
            $result['removed'],
            $result['moved_data'],
            $result['moved_narrations']
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'result' => $result]);
        }

        return back()->with('alert-success', $message);
    }

    public function duplicates(Request $request, KpiDeduplicationService $dedupe)
    {
        return view('admin.kpi.duplicates', [
            'indicator_groups' => $dedupe->findIndicatorDuplicateGroups(),
            'subject_area_groups' => $dedupe->findSubjectAreaDuplicateGroups(),
            'kpi_stats' => kpi_admin_stats(),
        ]);
    }

    protected function queueTask(Request $request, string $action, array $payload, string $queuedMessage)
    {
        $runs = app(KpiSyncRunService::class);
        $run = $runs->create($action, $payload, auth()->id());
        ProcessKpiOwidActionJob::dispatch($run->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'run_id' => $run->id,
                'message' => $queuedMessage,
            ]);
        }

        return back()
            ->with('kpi_sync_run_id', $run->id)
            ->with('alert-info', $queuedMessage.' Track progress below.');
    }

    protected function rejectOwidImport(Request $request)
    {
        $message = 'Our World in Data import is disabled. Use manual indicators and country values instead.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->with('alert-warning', $message);
    }
}
