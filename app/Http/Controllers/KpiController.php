<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Repositories\IndicatorsRepository;


class KpiController extends Controller
{
    private $indicatorsRepo;

    public function __construct(IndicatorsRepository $indicatorsRepo)
    {
        $this->indicatorsRepo = $indicatorsRepo;
    }

    public function index(Request $request)
    {
        $request['rows'] = $request->rows ?? 20; // Set pagination to 20 per page
        $data['search'] = (object) $request->all();
        $data['indicators'] = $this->indicatorsRepo->get($request);

        $subject_areas = $this->indicatorsRepo->get_subject_areas();

        $data['subject_areas'] = $subject_areas;

        return view('admin.kpi.index', $data);
    }

    public function save(Request $request)
    {
        $result = $this->indicatorsRepo->save($request);
        if ($request->id) {
            return back()->with('alert-success', 'Indicator updated successfully.');
        } else {
            return back()->with('alert-success', 'Indicator created successfully.');
        }
    }

    public function save_data(Request $request)
    {
        $result = $this->indicatorsRepo->save_data($request);
        
        if ($result['count'] > 0) {
            $message = $result['count'] . ' record(s) saved successfully.';
            if (!empty($result['errors'])) {
                $message .= ' However, ' . count($result['errors']) . ' error(s) occurred: ' . implode('; ', $result['errors']);
            }
            return back()->with('alert-success', $message);
        } else {
            return back()->with('alert-danger', 'No records were saved. Please ensure all required fields are filled.');
        }
    }

    public function data(Request $request)
    {
        $request['rows'] = $request->rows ?? 20; // Set pagination to 20 per page
        $data['search'] = (object) $request->all();
        $data['kpis']      = $this->indicatorsRepo->get_kpis();
        $data['countries'] = Country::where('national','National')->get();
        $data['kpi_data']  = $this->indicatorsRepo->get_kpi_data($request);

        return view('admin.kpi.data', $data);
    }

    public function get_data(Request $request)
    {
        $kpi_id = $request->kpi_id;
        $country_id = $request->country_id;
        $period = $request->period;
        
        $record = $this->indicatorsRepo->find_data($kpi_id, $country_id, $period);
        
        if ($record) {
            // Parse period to get year and month
            $periodStr = $record->period ?? '';
            $year = '';
            $month = '';
            if ($periodStr && strlen($periodStr) >= 7) {
                $parts = explode('-', $periodStr);
                if (count($parts) >= 2) {
                    $year = $parts[0];
                    $month = ltrim($parts[1], '0');
                    if (empty($month)) $month = '0';
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
                    // Store old values for update
                    'old_kpi_id' => $record->kpi_id,
                    'old_country_id' => $record->country_id,
                    'old_period' => $record->period,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Record not found'
        ], 404);
    }

    public function update_data(Request $request)
    {
        $result = $this->indicatorsRepo->update_data($request);
        
        if ($result['success']) {
            $message = 'Indicator data updated successfully.';
            if (isset($result['count']) && $result['count'] > 1) {
                $message .= ' (' . $result['count'] . ' records updated)';
            }
            return back()->with('alert-success', $message);
        } else {
            return back()->with('alert-danger', $result['message'] ?? 'Failed to update indicator data.');
        }
    }

    public function destroy_data(Request $request)
    {
        $kpi_id = $request->kpi_id;
        $country_id = $request->country_id;
        $period = $request->period;
        
        $deleted = $this->indicatorsRepo->delete_data($kpi_id, $country_id, $period);
        
        if ($deleted) {
            return back()->with('alert-success', 'Indicator data deleted successfully.');
        } else {
            return back()->with('alert-danger', 'Failed to delete indicator data.');
        }
    }

    public function get(Request $request)
    {
        $id = $request->id;
        $indicator = $this->indicatorsRepo->find($id);
        
        if ($indicator) {
            return response()->json([
                'success' => true,
                'indicator' => $indicator
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Indicator not found'
        ], 404);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $this->indicatorsRepo->delete($id);
        return true;
    }
}
