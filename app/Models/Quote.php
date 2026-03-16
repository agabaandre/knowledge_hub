<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['quote', 'image', 'link_url'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        $raw = $this->attributes['image'] ?? null;
        if (empty($raw)) {
            return null;
        }
        if (strpos($raw, 'http') === 0 || strpos($raw, '//') === 0) {
            return $raw;
        }
        return asset('storage/uploads/quotes/' . $raw);
    }
}
