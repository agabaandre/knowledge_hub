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
}
