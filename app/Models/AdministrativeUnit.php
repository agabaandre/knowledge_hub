<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'code',
        'alternate_code',
        'logo',
        'icon',
        'country_id',
        'iso_code',
        'iso3_code',
    ];

    public function parent(){

       return $this->belongsTo(AdministrativeUnit::class,"parent_id","id");
    }

    public function children()
    {
        return $this->hasMany(AdministrativeUnit::class, 'parent_id', 'id')
            ->orderBy('name')
            ->orderBy('id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function localities()
    {
        return $this->hasMany(Locality::class, 'administrative_unit_id', 'id');
    }
}
