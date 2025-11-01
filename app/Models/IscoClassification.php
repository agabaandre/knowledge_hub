<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IscoClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'isco_id',
        'name'
    ];

    public $timestamps = false;

    public function jobTitles()
    {
        return $this->hasMany(JobTitle::class, 'isco_id', 'isco_id');
    }

    public function experts()
    {
        return $this->hasMany(Expert::class, 'isco_classification_id', 'id');
    }

    /**
     * Scope to get unique ISCO classifications (removes duplicates by isco_id)
     */
    public function scopeUniqueByIscoId($query)
    {
        return $query->selectRaw('MIN(id) as id, isco_id, MIN(name) as name')
            ->groupBy('isco_id')
            ->orderBy('name');
    }
}
