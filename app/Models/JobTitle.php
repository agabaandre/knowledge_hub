<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'classification_id',
        'isco_id',
        'name'
    ];

    public function iscoClassification()
    {
        return $this->belongsTo(IscoClassification::class, 'isco_id', 'isco_id');
    }

    public function experts()
    {
        return $this->hasMany(Expert::class, 'job_title_id', 'id');
    }
}
