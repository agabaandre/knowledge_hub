<?php
namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsRepository{

    public function get(Request $request){

        return Setting::first()->toArray();
    }
    
    public function save(Request $request){

        $settings = Setting::find(1);

        $settings->site_name            = $request->site_name;
        $settings->title                = $request->title;
        $settings->site_description     = $request->site_description;
        $settings->seo_keywords         = $request->seo_keywords;
        $settings->address           = $request->address;
        $settings->phone             = $request->phone;
        $settings->email             = $request->email;
        $settings->timezone          = $request->timezone;
        $settings->primary_color     = $request->primary_color;
        $settings->secondary_color   = $request->secondary_color;
        $settings->analytics_script  = $request->analytics_script;
        $settings->slogan            = $request->slogan;
        $settings->primary_text_color = $request->primary_text_color;
        $settings->links_active_color = $request->links_active_color;
        $settings->icon_font_color = $request->icon_font_color;
        $settings->banner_text = $request->banner_text;

        //save cover
        if($request->hasFile('logo') || $request->hasFile('favicon')|| $request->hasFile('spotlight_banner')):

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
            if ($request->hasFile('spotlight_banner')):
                $banner_file = $request->file('spotlight_banner');
                $banner_filepath = $this->save_attachments($banner_file);
                $settings->spotlight_banner = $banner_filepath;
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