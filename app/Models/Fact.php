<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fact extends Model
{
    use HasFactory;

    protected $fillable = [
        'fact_title',
        'fact_summary',
        'fact_description',
        'fact_image',
        'resource_id',
        'is_ai_managed',
    ];

    protected $casts = [
        'is_ai_managed' => 'boolean',
    ];
}
