<?php

namespace App\Http\Controllers;

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

    public function data(Request $request)
    {
        $subject_areas = $this->indicatorsRepo->get_subject_areas();

        $data['subject_areas'] = $subject_areas;

        // $data['search'] = (object) $request->all();
        // $data['data'] = $this->indicatorsRepo->get_data($request);
        // return view('admin.kpi.data', $data);

        $data['kpi_data'] = $this->indicatorsRepo->get_kpi_data();
        return view('admin.kpi.data', $data);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $this->indicatorsRepo->delete($id);
        return true;
    }
}
