<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table ="country";

    // public function area(){
    //     return $this->belongsTo(GeoCoverage::class,"region_id","id");
    // }

    public function region(){
        return $this->belongsTo(Region::class,"region_id","id");
    }

    public function publications(){
        return $this->hasMany(Publication::class,"geographical_coverage_id","id");
    }
    
}
