<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'path',
        'name',
        'record_id',
    ];

    protected $hidden = [
        'model',
        'id',
    ];

    /**
     * Public URL for the file. The DB column must hold a relative path (e.g. {@code forums/name.pdf}).
     * Some legacy rows store an absolute URL, or {@code uploads/} + URL; avoid double-prefixing {@code storage_link}.
     */
    public function getPathAttribute($path)
    {
        if ($path === null || $path === '') {
            return '';
        }
        $path = trim((string) $path);

        // Doubled absolute URLs in one string (e.g. …/storage/uploads/https://…/forums/file.pdf) — keep the last URL.
        if (preg_match_all('#https?://#i', $path, $schemeMatches, PREG_OFFSET_CAPTURE) > 1) {
            $last = end($schemeMatches[0]);
            if (is_array($last) && isset($last[1])) {
                $path = trim(substr($path, (int) $last[1]));
            }
        }

        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }

        // First URL embedded in a mistaken "uploads/" + absolute URL value
        if (Str::contains($path, '://') && preg_match('#https?://[^\s"\'<>]+#i', $path, $m)) {
            return rtrim($m[0], '/');
        }

        $path = ltrim($path, '/');
        if (Str::startsWith($path, 'storage/')) {
            return storage_link($path);
        }
        if (Str::startsWith($path, 'uploads/')) {
            return storage_link($path);
        }

        return storage_link('uploads/'.$path);
    }

}
