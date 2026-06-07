<?php
namespace App\Repositories;

use App\Models\AdministrativeUnit;
use Illuminate\Http\Request;

class AdminUnitsRepository{

    public function get(Request $request){

        $records = AdministrativeUnit::orderBy('created_at','desc');

        if($request->term){
            $records->where('name','like',$request->term.'%');
        }

        return $records->paginate(15);
    }
    
    public function save(Request $request){

        $record = ($request->id)?AdministrativeUnit::find($request->id) : new AdministrativeUnit();

        $record->name            = $request->unit_name;
        $record->description     = $request->description;
        $record->parent_id       = ($request->parent_id)?$request->parent_id:null;
        $record->code            = $request->code;
        $record->alternate_code  = $request->alt_code;
        $record->icon            = $request->icon;

        if (\Illuminate\Support\Facades\Schema::hasColumn('administrative_units', 'country_id')) {
            $countryId = $request->input('country_id');
            $record->country_id = ($countryId !== null && $countryId !== '') ? (int) $countryId : null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('administrative_units', 'iso_code')) {
            $iso2 = strtoupper(trim((string) $request->input('iso_code', '')));
            $record->iso_code = $iso2 !== '' ? $iso2 : null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('administrative_units', 'iso3_code')) {
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

        return AdministrativeUnit::where('parent_id',$id)->get();
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

        return AdministrativeUnit::find($id)->delete();
    }

}