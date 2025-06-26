<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationCountry extends Model
{
    use HasFactory;

    protected $table = 'publication_countries';

    protected $fillable = [
        'publication_id',
        'country_id',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
