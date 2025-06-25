<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table ="country";

    public function region(){
        return $this->belongsTo(Region::class,"region_id","id");
    }

    public function publications()
    {
        return $this->hasMany(Publication::class, 'publication_countries', 'country_id', 'publication_id');
    }
    
}
