<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMultilingualTitles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    use NormalizesMultilingualTitles;

    protected $fillable = [
        'question',
        'answer',
    ];

    protected function multilingualTitleAttributes(): array
    {
        return ['question' => true];
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
