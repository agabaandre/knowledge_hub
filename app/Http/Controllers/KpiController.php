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

        $data['search'] = (object) $request->all();
        $data['indicators'] = $this->indicatorsRepo->get($request);

        $subject_areas = $this->indicatorsRepo->get_subject_areas();

        $data['subject_areas'] = $subject_areas;

        return view('admin.kpi.index', $data);
    }

    public function save(Request $request)
    {
        $this->indicatorsRepo->save($request);
        return back();
    }

    public function save_data(Request $request)
    {
        $this->indicatorsRepo->save_data($request);
        return back();
    }

    public function data(Request $request)
    {
        $data['search'] = (object) $request->all();
        $data['kpis']      = $this->indicatorsRepo->get_kpis();
        $data['countries'] = Country::where('national','National')->get();
        $data['kpi_data']  = $this->indicatorsRepo->get_kpi_data($request);

        return view('admin.kpi.data', $data);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $this->indicatorsRepo->delete($id);
        return true;
    }
}
