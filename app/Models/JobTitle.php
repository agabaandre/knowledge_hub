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
}
