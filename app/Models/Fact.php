<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMultilingualTitles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fact extends Model
{
    use HasFactory;
    use NormalizesMultilingualTitles;

    protected $fillable = [
        'fact_title',
        'fact_summary',
        'fact_description',
        'fact_image',
        'resource_id',
        'is_ai_managed',
    ];

    protected function multilingualTitleAttributes(): array
    {
        return [
            'fact_title' => true,
            'fact_summary' => true,
        ];
    }

    protected $casts = [
        'is_ai_managed' => 'boolean',
    ];
}
