<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\User;

class SharedRepo{


    public function access_filter($query,$use_country_id=false,$use_query_builder=false){

        $user = @current_user();

        if($user && $user->access_level){
    
            $level   = $user->access_level;
            $country = $user->country;

            $col   = "author_id"; //column to filter with
            $table = ($use_query_builder)?$query->from : $query->getModel()->getTable();
            $use_user_id = false;

            $country_col ="country_id";

            if($table =="author"):
               $col = "id"; //change filter col
             elseif($table =="forums"  || $table=="forum_comments"):
               $col = "created_by"; //filter column
               $use_user_id = true; //user id from users instead of author_id
             elseif($table  == "experts" || $table  == "data_records"):
               $use_country_id = true; //directly use country id
             elseif($table =="country"):
                $country_col ="id";
             endif;

           
            if($level->level_name     == "Viewer" || $level->level_name == "Country"):
             // get for own country
            
                if($use_country_id && states_enabled()):
                    $query->where($country_col,$user->country_id);
                elseif($use_user_id):
                    $authors = $this->get_country_users($user->country_id);
                    $query->whereIn($col,$authors);
                else:
                    $authors = $this->get_country_authors($user->country_id);
                    $query->whereIn($col,$authors);
                endif;
             
            elseif($level->level_name == "RCC" && states_enabled()):
    
             
                if($use_country_id):
                    $query->where($country_col,$user->country_id);
                elseif($use_user_id):
                    $authors = $this->get_country_users($user->country_id);
                    $query->whereIn($col,$authors);
                 else:
                    $authors = $this->get_country_authors($user->region_id,true);
                    $query->whereIn($col,$authors);    
                 endif;

                 // get for countries in rcc
            elseif($level->level_name == "Overall"):
                // get normally
            endif;
    
        }

        return $query;
    
    }
    

    public function get_country_authors($id,$is_region=false){

        if( is_array($is_region) && states_enabled()):
            $countries = Country::where('region_id',$id->region_id)->get()->pluck('id');
            return User::whereIn('country_id',$countries)->get()->pluck('author_id');
        else:
            return User::where('country_id',$id)->get()->pluck('author_id');
        endif;
    }

    private function get_country_users($id,$is_region=false){

        if( is_array($is_region) && states_enabled()):
            $countries = Country::where('region_id',$id->region_id)->get()->pluck('id');
            return User::whereIn('country_id',$countries)->get()->pluck('id');
        else:
            return User::where('country_id',$id)->get()->pluck('id');
        endif;
    }
    

    

}