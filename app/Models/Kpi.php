<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $table = "kpi";
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_area_id',
        'frequency'
    ];
}
