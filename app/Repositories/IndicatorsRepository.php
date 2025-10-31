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

        $rows_count = ($request->rows) ? $request->rows : 24;
        $kpis = Kpi::with('subjectArea');
        $kpis->orderBy('id', 'desc');
        $kpis->get();

        if ($request->term) {
            $kpis->where('name', 'like', '%' . $request->term . '%');
            $kpis->orWhere('name', 'like', '%' . $request->term . '%');
        }

        $kpis = $kpis->paginate($rows_count);

        return $kpis;
    }

    public function save(Request $request)
    {

        $kpi = new Kpi();
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
        $data = KpiData::orderBy('period_year','desc')->orderBy('period_month','desc')->orderBy('kpi_id','desc');

        if($request && $request->country_id)
        $data->where('country_id',intval($request->country_id));

        $data = $data->paginate(15);
        return $data;
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
