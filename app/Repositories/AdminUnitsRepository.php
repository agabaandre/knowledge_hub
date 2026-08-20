<?php
namespace App\Repositories;

use App\Models\AdministrativeUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminUnitsRepository{

    public function get(Request $request){

        $records = AdministrativeUnit::query()->with('parent');

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            $records->where(function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%')
                    ->orWhere('code', 'like', '%'.$term.'%')
                    ->orWhere('alternate_code', 'like', '%'.$term.'%');
            });
        }

        if ($request->filled('parent_id')) {
            $records->where('parent_id', (int) $request->parent_id);
        }

        $sort = (string) $request->input('sort', 'name');
        $dir = strtolower((string) $request->input('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowed = ['name', 'description', 'code', 'created_at', 'id'];
        if (! in_array($sort, $allowed, true)) {
            $sort = 'name';
        }

        $records->orderBy($sort, $dir)->orderBy('id', 'asc');

        // Admin DataTables: full filtered set for client-side sort/search/page.
        if ($request->boolean('datatable') || $request->input('rows') === 'all') {
            return $records->get();
        }

        $rows = (int) ($request->rows ?: 25);
        if ($rows <= 0) {
            $rows = 25;
        }

        return $records->paginate($rows)->appends($request->only(['term', 'parent_id', 'sort', 'dir', 'rows']));
    }
    
    public function save(Request $request){

        $record = ($request->id)?AdministrativeUnit::find($request->id) : new AdministrativeUnit();
        if (! $record) {
            return false;
        }

        $parentId = $request->filled('parent_id') ? (int) $request->parent_id : null;
        // A unit cannot be its own parent; siblings under the same parent are allowed.
        if ($parentId && (int) $record->id === $parentId) {
            $parentId = null;
        }

        $record->name            = $request->unit_name;
        $record->description     = $request->description;
        $record->parent_id       = $parentId;
        $record->code            = $request->code;
        $record->alternate_code  = $request->alt_code;
        $record->icon            = $request->icon;

        if (Schema::hasColumn('administrative_units', 'country_id')) {
            $countryId = $request->input('country_id');
            $record->country_id = ($countryId !== null && $countryId !== '') ? (int) $countryId : null;
        }
        if (Schema::hasColumn('administrative_units', 'iso_code')) {
            $iso2 = strtoupper(trim((string) $request->input('iso_code', '')));
            $record->iso_code = $iso2 !== '' ? $iso2 : null;
        }
        if (Schema::hasColumn('administrative_units', 'iso3_code')) {
            $iso3 = strtoupper(trim((string) $request->input('iso3_code', '')));
            $record->iso3_code = $iso3 !== '' ? $iso3 : null;
        }

        //save cover
        if($request->hasFile('logo')):

            if($request->hasFile('logo')):
                $logo_file           = $request->file('logo');
                $logo_filepath       = $this->save_attachments($logo_file);
                $record->logo     = $logo_filepath;
            endif;

        endif;

        $saved = ($request->id)?$record->update():$record->save();

        if ($saved) {
            cache()->forget('adminunits');
        }

        return $saved;
    }

    private function save_attachments($files){

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        
        foreach ($upfiles as $file):

            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
            $file->move(storage_path().'/app/public/uploads/adminunits/',$file_path);

        endforeach;

       return $file_path;
    }


    public function find($id){

        return AdministrativeUnit::find($id);
    }


    public function child_units($id){

        // Multiple children at the same level under one parent are supported.
        return AdministrativeUnit::where('parent_id',$id)->orderBy('name')->orderBy('id')->get();
    }

    /**
     * Choropleth points for the hub owner's admin-unit map (joined by ISO alpha-3).
     */
    public function get_map_values(): array
    {
        $units = AdministrativeUnit::query()
            ->whereNotNull('iso3_code')
            ->where('iso3_code', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'iso3_code', 'iso_code']);

        if ($units->isEmpty()) {
            return [
                'unit_count' => 0,
                'points' => [],
                'min' => null,
                'max' => null,
            ];
        }

        $userCounts = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotNull('administrative_unit_id')
            ->groupBy('administrative_unit_id')
            ->selectRaw('administrative_unit_id, count(*) as total')
            ->pluck('total', 'administrative_unit_id');

        $localityCounts = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('localities')) {
            $localityCounts = \Illuminate\Support\Facades\DB::table('localities')
                ->groupBy('administrative_unit_id')
                ->selectRaw('administrative_unit_id, count(*) as total')
                ->pluck('total', 'administrative_unit_id');
        }

        $points = [];
        $numericValues = [];

        foreach ($units as $unit) {
            $iso3 = strtoupper(trim((string) $unit->iso3_code));
            if ($iso3 === '') {
                continue;
            }

            $members = (int) ($userCounts[$unit->id] ?? 0);
            $localities = (int) ($localityCounts[$unit->id] ?? 0);
            $value = $members + $localities;
            $numericValues[] = $value;

            $points[] = [
                'iso-a3' => $iso3,
                'hc-key' => strtolower((string) ($unit->iso_code ?? '')),
                'unit_id' => (int) $unit->id,
                'name' => $unit->name,
                'value' => $value,
                'display_value' => $value.' members/localities',
                'detail_url' => url('adminunits/details?id='.$unit->id),
            ];
        }

        return [
            'unit_count' => count($points),
            'points' => $points,
            'min' => $numericValues === [] ? null : min($numericValues),
            'max' => $numericValues === [] ? null : max($numericValues),
        ];
    }

    public function delete($id){

        $unit = AdministrativeUnit::find($id);
        if (! $unit) {
            return false;
        }

        $deleted = $unit->delete();
        if ($deleted) {
            cache()->forget('adminunits');
        }

        return $deleted;
    }

}
