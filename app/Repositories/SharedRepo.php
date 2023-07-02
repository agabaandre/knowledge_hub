<?php

namespace App\Repositories;

use App\Models\Country;

class SharedRepo{


    public function access_filter($query){

        $user = @current_user();
    
        if($user && $user->access_level){
    
            $level   = $user->access_level;
            $country = $user->country;
    
            if($level->access_level_name     == "Viewer" || $level->access_level_name == "Country"):
             // get for own country
             $query->where('country_id',$user->country_id);
             
            elseif($level->access_level_name == "RCC"):
    
                $countries = Country::where('region_id',$country->region_id)->get()->pluck('id');
                $query->whereIn('country_id',$countries);
    
                 // get for countries in rcc
            elseif($level->access_level_name == "Overall"):
                // get normally
            endif;
    
        }

        return $query;
    
    }
    
    

}