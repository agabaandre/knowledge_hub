<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table ="country";

    public function area(){
        return $this->belongsTo(GeoCoverage::class,"region_id","id");
    }
    
}
