<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMultilingualTitles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    use NormalizesMultilingualTitles;

    protected $fillable = [
        'title','description','venue','startdate','enddate','organized_by','fee','status',
        'event_link','registration_link','is_online','contact_person','orcid','banner_image','country_id',
        'created_by','updated_by','community_of_practice_id','event_category'
    ];

    protected function multilingualTitleAttributes(): array
    {
        return [
            'title' => true,
            'venue' => true,
            'organized_by' => true,
            'contact_person' => true,
        ];
    }

    public function tags()
    {
        return $this->hasMany(EventTag::class);
    }

    public function community()
    {
        return $this->belongsTo(CommunityOfPractice::class, 'community_of_practice_id');
    }
}
