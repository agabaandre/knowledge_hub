<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectArea extends Model
{
    use HasFactory;

    protected $table = 'subject_areas';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'owid_topic',
        'owid_search_query',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class, 'subject_area', 'id');
    }
}
