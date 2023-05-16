<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRecord extends Model
{
    use HasFactory;

    public function sub_category(){
        return $this->belongsTo(DataSubCategory::class,"data_sub_category_id","id");
    }

    public function country(){
        return $this->belongsTo(Country::class,"country_id","id");
    }
    
}
