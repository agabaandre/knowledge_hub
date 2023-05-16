<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSubCategory extends Model
{
    use HasFactory;

    public function category(){

        return $this->belongsTo(DataCategory::class,"data_category_id","id");
    }
}
