<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Convenience: infer model accessed from available fields
    public function getModelNameAttribute(): string
    {
        if (!empty($this->publication_id)) {
            return 'Publication';
        }
        return '-';
    }
}
