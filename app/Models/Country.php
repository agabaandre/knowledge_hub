<?php

namespace App\Models;

use App\Rules\InternationalPhoneKnownCallingCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Country extends Model
{
    use HasFactory;

    protected $table ="country";

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget(InternationalPhoneKnownCallingCode::CACHE_KEY);
        });

        static::deleted(function () {
            Cache::forget(InternationalPhoneKnownCallingCode::CACHE_KEY);
        });
    }

    public function region(){
        return $this->belongsTo(Region::class,"region_id","id");
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_countries', 'country_id', 'publication_id');
    }
    
}
