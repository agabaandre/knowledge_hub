<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Kpi;
use App\Models\KpiData;
use App\Models\KpiDataRecord;
use App\Models\SubjectArea;
use Illuminate\Http\Request;

class IndicatorsRepository
{

    public function get(Request $request)
    {
        $rows_count = ($request->rows) ? $request->rows : 20;
        $kpis = Kpi::with('subjectArea');

        if ($request->status) {
            $kpis->where('status', $request->status);
        }

        if ($request->source) {
            $kpis->where('source', $request->source);
        }
        
        if ($request->term) {
            $kpis->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->term . '%')
                      ->orWhere('description', 'like', '%' . $request->term . '%');
            });
        }

        $kpis->orderBy('id', 'desc');
        
        return $kpis->paginate($rows_count)->appends($request->all());
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
        return Kpi::destroy($id);
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
