<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends Model
{
    protected $table = 'kpi';
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_area',
        'computation_method',
        'frequency',
        'owid_chart_slug',
        'owid_url',
        'owid_variant_name',
        'unit_label',
        'source',
        'status',
        'approved_by',
        'approved_at',
        'recalled_at',
        'last_synced_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'approved_at' => 'datetime',
        'recalled_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function subjectArea()
    {
        return $this->belongsTo(SubjectArea::class, 'subject_area');
    }

    public function dataRecords(): HasMany
    {
        return $this->hasMany(KpiDataRecord::class, 'kpi_id');
    }

    public function narrations(): HasMany
    {
        return $this->hasMany(KpiNarration::class, 'kpi_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
