<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = [
        'administrative_unit_id',
        'name',
        'code',
        'iso3_code',
        'latitude',
        'longitude',
    ];

    public function administrativeUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'administrative_unit_id', 'id');
    }
}
