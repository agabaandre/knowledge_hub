<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationAttachment extends Model
{
    use HasFactory;

    public function getFileAttribute($value)
    {
        return storage_link('uploads/publications/' . $value);
    }

    /** Full URL for this attachment (for ChatPDF add-url). */
    public function getFileUrlAttribute()
    {
        return $this->file;
    }

    /** Absolute path on disk (for ChatPDF add-file when URL not public). */
    public function getFilePathAttribute()
    {
        $raw = $this->getRawOriginal('file');
        if (empty($raw)) {
            return null;
        }
        $path = storage_path('app/public/uploads/publications/' . $raw);
        return file_exists($path) ? $path : null;
    }

    /** True if this attachment is a PDF. */
    public function getIsPdfAttribute()
    {
        $raw = $this->getRawOriginal('file');
        return $raw && strpos(strtolower($raw), '.pdf') !== false;
    }
}
