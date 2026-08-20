<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AdminUnitsRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminUnitsController extends Controller
{
    private $adminUnitsRepository;

    public function __construct(AdminUnitsRepository $adminUnitsRepository)
    {
        $this->adminUnitsRepository = $adminUnitsRepository;
    }

    public function index(Request $request)
    {
        $request->merge(['datatable' => true]);
        $data['adminunits'] = $this->adminUnitsRepository->get($request);
        $data['search'] = (object) $request->all();

        $themesRepo = app(\App\Repositories\ThemesRepository::class);
        $data['faIconOptions'] = $themesRepo->fontAwesomeIconOptions();
        $data['faVersion'] = $themesRepo->fontAwesomeVersion();
        $data['faCheatsheetUrl'] = $themesRepo->fontAwesomeCheatsheetUrl();
        $data['defaultUnitIcon'] = 'fa-building';
        $data['defaultOwnerCountryId'] = function_exists('hub_owner_country_id') ? hub_owner_country_id() : null;

        return view('admin.adminunits.index', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'id' => ['nullable', 'integer', 'exists:administrative_units,id'],
            'unit_name' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:300'],
            'code' => ['nullable', 'string', 'max:50'],
            'alt_code' => ['nullable', 'string', 'max:50'],
            'icon' => ['required', 'string', 'max:50'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:administrative_units,id',
                Rule::notIn([(int) $request->input('id')]),
            ],
            'logo' => ['nullable', 'image', 'max:4096'],
        ];

        if (Schema::hasColumn('administrative_units', 'country_id')) {
            $rules['country_id'] = ['nullable', 'integer', 'exists:countries,id'];
        }
        if (Schema::hasColumn('administrative_units', 'iso_code')) {
            $rules['iso_code'] = ['nullable', 'string', 'size:2'];
        }
        if (Schema::hasColumn('administrative_units', 'iso3_code')) {
            $rules['iso3_code'] = ['nullable', 'string', 'size:3'];
        }

        $request->validate($rules, [
            'unit_name.required' => 'Admin unit name is required.',
            'icon.required' => 'Please select an icon.',
            'parent_id.not_in' => 'A unit cannot be its own parent.',
        ]);

        $saved = $this->adminUnitsRepository->save($request);

        if ($saved) {
            $flash = [
                'message' => 'Unit saved successfully',
                'status' => 'success',
                'alert_class' => 'success',
                'data' => $saved,
            ];
        } else {
            $flash = [
                'message' => 'Operation failed, try again',
                'status' => 'failure',
                'alert_class' => 'danger',
                'data' => $saved,
            ];
        }

        if ($request->ajax()) {
            return response($flash, 200);
        }

        if ($saved) {
            return back()->with($flash);
        }

        return back()->with($flash)->withInput($request->except(['logo']));
    }

    public function destroy(Request $request)
    {
        return $this->adminUnitsRepository->delete($request->id);
    }
}
