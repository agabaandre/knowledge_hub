<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'path',
        'name',
        'stored_filename',
        'record_id',
    ];

    protected $hidden = [
        'model',
        'id',
    ];

    /**
     * Public URL for the stored file. DB `path` is normally relative (e.g. `forums/abc.pdf`).
     * Some rows store a full URL or already include `uploads/` — avoid prefixing `uploads/` again
     * (which produced `/storage/uploads/https://…/storage/uploads/…`).
     */
    public function getPathAttribute($path)
    {
        if ($path === null || $path === '') {
            return '';
        }
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return storage_link($path);
        }
        // Legacy bad rows: `uploads/https://…` — strip the mistaken prefix.
        if (preg_match('#^uploads/(https?://)#i', $path)) {
            return storage_link(substr($path, strlen('uploads/')));
        }
        if (str_starts_with($path, 'uploads/')) {
            return storage_link($path);
        }

        return storage_link('uploads/'.$path);
    }

}
