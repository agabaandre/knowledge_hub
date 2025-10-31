<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','venue','startdate','enddate','organized_by','fee','status',
        'event_link','registration_link','is_online','contact_person','orcid','banner_image','country_id',
        'created_by','updated_by'
    ];

    public function tags()
    {
        return $this->hasMany(EventTag::class);
    }
}
