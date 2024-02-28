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

        //save cover
        if($request->hasFile('logo')):

            if($request->hasFile('logo')):
                $logo_file           = $request->file('logo');
                $logo_filepath       = $this->save_attachments($logo_file);
                $record->logo     = $logo_filepath;
            endif;

        endif;

        $record->save();

        return $record;
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

    public function delete($id){

        return AdministrativeUnit::find($id)->delete();
    }

}