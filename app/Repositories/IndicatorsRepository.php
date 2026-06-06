<?php

namespace App\Repositories;

use App\Models\Kpi;
use App\Models\KpiData;
use App\Models\KpiDataRecord;
use App\Models\KpiNarration;
use App\Models\SubjectArea;
use App\Services\Owid\OwidIndicatorSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicatorsRepository
{

    public function get(Request $request)
    {
        $rows_count = ($request->rows) ? $request->rows : 20;

        return $this->buildAdminIndicatorsQuery($request)
            ->orderBy('id', 'desc')
            ->paginate($rows_count)
            ->appends($request->all());
    }

    public function buildAdminIndicatorsQuery(Request $request)
    {
        $kpis = Kpi::query()->with('subjectArea')->withCount(['dataRecords', 'narrations']);

        if ($request->filled('status')) {
            $kpis->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $kpis->where('source', $request->source);
        }

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            $kpis->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if ($request->boolean('duplicates')) {
            $duplicateIds = collect(app(\App\Services\Kpi\KpiDeduplicationService::class)->findIndicatorDuplicateGroups())
                ->flatMap(fn ($group) => collect($group['members'])->pluck('id'))
                ->unique()
                ->values()
                ->all();
            $kpis->whereIn('id', $duplicateIds ?: [0]);
        }

        return $kpis;
    }

    public function adminIndicatorsDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 20)), 100);

        $base = $this->buildAdminIndicatorsQuery($request);
        $recordsTotal = Kpi::query()->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 2);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [
            1 => 'id',
            2 => 'name',
            3 => 'description',
            4 => 'subject_area',
            5 => 'frequency',
            6 => 'status',
        ];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        $manualOnly = function_exists('kpi_manual_data_only') && kpi_manual_data_only();

        $data = [];
        $index = $start + 1;

        foreach ($rows as $row) {
            $status = (string) ($row->status ?? 'draft');
            $isPublished = $status === 'published';
            $rowClass = $isPublished ? 'kpi-row-published' : ($status === 'recalled' ? 'kpi-row-recalled' : '');

            $title = '<div class="kpi-cell-wrap"><strong>'.e($row->name ?? 'N/A').'</strong>';
            if ($owidUrl = owid_chart_url($row)) {
                $title .= '<br><a href="'.e($owidUrl).'" target="_blank" rel="noopener noreferrer" class="small">View on Our World in Data</a>';
            }
            $title .= '</div>';

            $description = e(\Illuminate\Support\Str::limit(strip_tags((string) ($row->description ?? '')), 80));
            $subjectArea = e($row->subjectArea?->name ?? 'N/A');
            $frequency = '<span class="badge badge-info">'.e($row->frequency ?? 'N/A').'</span>';

            $statusBadge = match ($status) {
                'published' => 'success',
                'recalled' => 'warning',
                default => 'secondary',
            };
            $statusHtml = '<span class="badge badge-'.$statusBadge.'">'.e(ucfirst($status)).'</span>';
            if (($row->source ?? '') === 'owid') {
                $statusHtml .= ' <span class="badge badge-light border">OWID</span>';
            }

            $actions = '<div class="kpi-actions-group">';
            if (! $isPublished) {
                $actions .= '<button type="button" class="btn btn-sm btn-success kpi-publish-one" data-id="'.$row->id.'" title="Publish on member state pages"><i class="fa fa-check"></i></button>';
            }
            if ($isPublished) {
                $actions .= '<button type="button" class="btn btn-sm btn-warning kpi-recall-one" data-id="'.$row->id.'" title="Recall from member state pages"><i class="fa fa-undo"></i></button>';
            }
            if (($row->source ?? '') === 'owid' && ! $manualOnly) {
                $actions .= '<button type="button" class="btn btn-sm btn-outline-secondary kpi-sync-one" data-id="'.$row->id.'" title="Refresh OWID values"><i class="fa fa-sync"></i></button>';
            }
            $actions .= '<a href="'.url('admin/kpi/data?kpi_id='.$row->id).'" class="btn btn-sm btn-outline-info" title="Country values"><i class="fa fa-table"></i></a>';
            $actions .= '<button type="button" class="btn btn-sm btn-outline-primary kpi-edit-one" data-id="'.$row->id.'" title="Edit"><i class="fa fa-edit"></i></button>';
            $actions .= '<button type="button" class="btn btn-sm btn-outline-danger kpi-delete-one" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></button>';
            $actions .= '</div>';

            $data[] = [
                'DT_RowClass' => trim($rowClass),
                'checkbox' => '<input type="checkbox" name="selected_ids[]" value="'.$row->id.'" class="kpi-indicator-checkbox">',
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => $title,
                'description' => '<div class="kpi-cell-wrap">'.$description.'</div>',
                'subject_area' => '<div class="kpi-cell-wrap">'.$subjectArea.'</div>',
                'frequency' => $frequency,
                'status' => $statusHtml,
                'values_count' => '<span class="text-muted">'.(int) ($row->data_records_count ?? 0).'</span>',
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

    public function bulkPublishManual(array $ids, ?int $userId = null): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return 0;
        }

        return Kpi::query()
            ->whereIn('id', $ids)
            ->where('status', '!=', 'published')
            ->update([
                'status' => 'published',
                'approved_by' => $userId,
                'approved_at' => now(),
                'recalled_at' => null,
            ]);
    }

    public function bulkRecall(array $ids, ?OwidIndicatorSyncService $sync = null): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return 0;
        }

        $sync = $sync ?? app(OwidIndicatorSyncService::class);
        $count = 0;
        foreach (Kpi::query()->whereIn('id', $ids)->where('status', 'published')->get() as $kpi) {
            $sync->recall($kpi);
            $count++;
        }

        return $count;
    }

    public function bulkDelete(array $ids): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return 0;
        }

        return DB::transaction(function () use ($ids) {
            KpiDataRecord::query()->whereIn('kpi_id', $ids)->delete();
            KpiNarration::query()->whereIn('kpi_id', $ids)->delete();
            return Kpi::query()->whereIn('id', $ids)->delete();
        });
    }

    public function save(Request $request)
    {
        // Check if updating existing KPI or creating new one
        if ($request->id) {
            $kpi = Kpi::find($request->id);
            if (!$kpi) {
                return null;
            }
        } else {
            $kpi = new Kpi();
        }

        $kpi->name         = $request->name;
        $kpi->description  = $request->description;
        $kpi->subject_area = $request->subject_area;
        $kpi->frequency    = $request->frequency;
        $kpi->save();

        return $kpi;
    }

    public function save_data(Request $request)
    {
        $saved = [];
        $errors = [];
        
        // Handle multiple rows (Excel-like table format)
        if ($request->has('data') && is_array($request->data)) {
            foreach ($request->data as $index => $row) {
                // Skip empty rows
                if (empty($row['country_id']) || empty($row['kpi_id']) || empty($row['year']) || empty($row['month']) || empty($row['indicator_value'])) {
                    continue;
                }
                
                try {
                    $kpi = new KpiDataRecord();
                    $kpi->kpi_id     = $row['kpi_id'];
                    $kpi->country_id = $row['country_id'];
                    $kpi->value      = $row['indicator_value'];
                    $kpi->period     = $row['year']."-".str_pad($row['month'], 2, '0', STR_PAD_LEFT);
                    $kpi->save();
                    $saved[] = $kpi;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }
        } else {
            // Handle single record (backward compatibility)
            $kpi = new KpiDataRecord();
            $kpi->kpi_id     = $request->kpi_id;
            $kpi->country_id = $request->country_id;
            $kpi->value      = $request->indicator_value;
            $kpi->period     = $request->year."-".str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $kpi->save();
            $saved[] = $kpi;
        }
        
        return [
            'saved' => $saved,
            'errors' => $errors,
            'count' => count($saved)
        ];
    }


    public function get_data(Request $request)
    {
        $data = KpiData::paginate(20);
        return $data;
    }

    public function get_kpi_data(Request $request = null)
    {
        $rows_count = ($request && $request->rows) ? $request->rows : 20;
        $data = KpiData::with('kpi')->orderBy('period_year','desc')->orderBy('period_month','desc')->orderBy('kpi_id','desc');

        if($request && $request->country_id) {
            $data->where('country_id',intval($request->country_id));
        }

        if($request && $request->kpi_id) {
            $data->where('kpi_id',intval($request->kpi_id));
        }

        return $data->paginate($rows_count)->appends($request ? $request->all() : []);
    }

    public function find_data($kpi_id, $country_id, $period)
    {
        // Find a record by composite key (kpi_id, country_id, period)
        // Since the view aggregates, we get the first matching record
        return KpiDataRecord::where('kpi_id', $kpi_id)
            ->where('country_id', $country_id)
            ->where('period', $period)
            ->first();
    }

    public function update_data(Request $request)
    {
        // Find records by old kpi_id, country_id, period
        $old_period = $request->old_period;
        $records = KpiDataRecord::where('kpi_id', $request->old_kpi_id)
            ->where('country_id', $request->old_country_id)
            ->where('period', $old_period)
            ->get();

        if ($records->isEmpty()) {
            return ['success' => false, 'message' => 'Record not found'];
        }

        $new_period = $request->year."-".str_pad($request->month, 2, '0', STR_PAD_LEFT);
        
        // Update all matching records (in case there are duplicates)
        foreach ($records as $record) {
            $record->kpi_id     = $request->kpi_id;
            $record->country_id = $request->country_id;
            $record->value      = $request->indicator_value;
            $record->period     = $new_period;
            $record->save();
        }

        return ['success' => true, 'count' => $records->count()];
    }

    public function delete_data($kpi_id, $country_id, $period)
    {
        // Delete all records matching the composite key
        $deleted = KpiDataRecord::where('kpi_id', $kpi_id)
            ->where('country_id', $country_id)
            ->where('period', $period)
            ->delete();

        return $deleted > 0;
    }

    public function find($id)
    {
        return Kpi::find($id);
    }

    public function delete($id)
    {
        return $this->bulkDelete([(int) $id]) > 0;
    }


    // Get Subject Areas For Ajax
    public function get_subject_areas()
    {

        $subject_areas = SubjectArea::all();
        return $subject_areas;
    }

    public function get_kpis()
    {
        $kpis = Kpi::all();
        return $kpis;
    }

}
