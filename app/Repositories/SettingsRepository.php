<?php
namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SettingsRepository{

    public function get(Request $request){

        // Always get the active configuration
        $setting = Setting::where('status', 'active')->first();
        
        // If no active setting exists, get the first one (fallback)
        if (!$setting) {
            $setting = Setting::first();
        }
        
        return $setting ? $setting->toArray() : [];
    }
    
    public function save(Request $request){

        // Get or create the active configuration
        $settings = Setting::where('status', 'active')->first();
        
        // If no active setting exists, get the first one or create a new one
        if (!$settings) {
            $settings = Setting::first();
            if (!$settings) {
                $settings = new Setting();
                $settings->config_name = $request->config_name ?? 'Default Configuration';
                $settings->status = 'active';
                $settings->save();
            } else {
                // Make the first one active if none is active
                $settings->status = 'active';
                $settings->save();
            }
        }

        // Update config_name if provided
        if ($request->has('config_name')) {
            $settings->config_name = $request->config_name;
        }

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
        $settings->footer_style = $request->footer_style;
        $settings->site_theme   = $request->site_theme;
        $settings->content_disclaimer = $request->content_disclaimer;
        $settings->gradient_start_color = $request->gradient_start_color;
        $settings->gradient_end_color = $request->gradient_end_color;
        // feature flags
        if ($request->has('menu_icons_enabled')) {
            $settings->menu_icons_enabled = (bool)$request->menu_icons_enabled;
        } else {
            // unchecked checkbox doesn't submit; set false
            $settings->menu_icons_enabled = false;
        }

        // Homepage content toggles (default false when unchecked)
        $settings->show_featured = (bool)$request->boolean('show_featured', false);
        $settings->show_events = (bool)$request->boolean('show_events', false);
        $settings->show_top_searches = (bool)$request->boolean('show_top_searches', false);
        $settings->show_tags = (bool)$request->boolean('show_tags', false);
        $settings->show_quotes = (bool)$request->boolean('show_quotes', false);
        $settings->show_quiz = (bool)$request->boolean('show_quiz', false);

        // Social login toggles (default false when unchecked)
        // Only set if columns exist to avoid errors on production
        if (Schema::hasColumn('setting', 'enable_microsoft_login')) {
            $settings->enable_microsoft_login = (bool)$request->boolean('enable_microsoft_login', false);
        }
        if (Schema::hasColumn('setting', 'enable_google_login')) {
            $settings->enable_google_login = (bool)$request->boolean('enable_google_login', false);
        }
        if (Schema::hasColumn('setting', 'enable_linkedin_login')) {
            $settings->enable_linkedin_login = (bool)$request->boolean('enable_linkedin_login', false);
        }

        // Publication form settings
        if (Schema::hasColumn('setting', 'publication_min_words')) {
            $settings->publication_min_words = (int)$request->input('publication_min_words', 150);
        }
        
        if (Schema::hasColumn('setting', 'publication_required_fields')) {
            $requiredFields = $request->input('required_fields', []);
            $settings->publication_required_fields = json_encode($requiredFields);
        }

        // Version submission setting
        if (Schema::hasColumn('setting', 'enable_version_submission')) {
            $settings->enable_version_submission = (bool)$request->boolean('enable_version_submission', true);
        }

        // Handle status change - if setting a new config as active, deactivate others
        if ($request->has('status') && $request->status === 'active') {
            // Deactivate all other settings
            Setting::where('id', '!=', $settings->id)->update(['status' => 'inactive']);
            $settings->status = 'active';
        }

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

        clear_cache();

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
