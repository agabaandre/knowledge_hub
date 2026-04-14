<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFont extends Model
{
    protected $fillable = ['name', 'font_family', 'font_files'];

    protected $casts = [
        'font_files' => 'array',
    ];

    /** CSS font-family value for use in styles */
    public function getFontFamilyCss(): string
    {
        return '"' . str_replace('"', '\\"', $this->font_family) . '", sans-serif';
    }

    /** Full URL for a stored font file key (woff2, woff, ttf) */
    public function getFileUrl(string $key): ?string
    {
        $files = $this->font_files ?? [];
        $path = $files[$key] ?? null;
        if (!$path) {
            return null;
        }
        return asset('storage/uploads/fonts/' . $path);
    }
}
