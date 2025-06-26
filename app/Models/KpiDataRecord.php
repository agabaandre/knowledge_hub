<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;

class KpiDataRecord extends Model
{
    use HasFactory;
    protected $table ="data";
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}
