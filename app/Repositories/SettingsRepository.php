<?php
namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsRepository{

    public function get(Request $request){

        return Setting::first()->toArray();
    }
    
    public function save(Request $request){

        $settings = new Setting();

        $settings->site_name            = $request->name;
        $settings->title                = $request->title;
        $settings->site_description     = $request->site_description;
        $settings->seo_keywords         = $request->seo_keywords;
        $settings->address      = $request->address;
        $settings->phone        = $request->contact;
        $settings->email        = $request->email;
        $settings->timezone     = $request->timezone;
        $settings->primary_color   = $request->primary_color;
        $settings->secondary_color = $request->secondary_color;

        //save cover
        if($request->hasFile('logo') || $request->hasFile('favicon')):

            if($request->hasFile('logo')):
                $logo_file           = $request->file('logo');
                $logo_filepath       = $this->save_attachments($logo_file);
                $settings->logo     = $logo_filepath;
            endif;

            if($request->hasFile('favicon')):
                $favicon_file     = $request->file('favicon');
                $favicon_filepath = $this->save_attachments($favicon_file);
                $settings->favicon  = $favicon_filepath;
            endif;

        endif;

        $settings->save();

        return $settings;
    }

    private function save_attachments($files){

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        
        foreach ($upfiles as $file):

            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
            $file->move(storage_path().'/app/public/uploads/config/',$file_path);

        endforeach;

       return $file_path;
    }

}