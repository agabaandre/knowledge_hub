<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicationAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'file',
        'description',
        'original_filename',
    ];

    public function getFileAttribute($value)
    {
        $raw = $value ?? '';
        if ($raw === '') {
            return storage_link('uploads/publications/');
        }
        $resolved = resolve_publication_upload_disk_path($raw);
        if ($resolved) {
            return storage_link('uploads/publications/' . basename($resolved));
        }
        $forUrl = normalize_publication_stored_filename_for_public_url($raw) ?? $raw;

        return storage_link('uploads/publications/' . $forUrl);
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

        return resolve_publication_upload_disk_path($raw);
    }

    /** True if this attachment is a PDF. */
    public function getIsPdfAttribute()
    {
        $raw = $this->getRawOriginal('file');

        return $raw && publication_filename_is_pdf($raw);
    }

    /** Suggested download name: human label + stored extension (stored disk name stays Laravel hash). */
    public function getDownloadFilenameAttribute()
    {
        $raw = $this->getRawOriginal('file') ?? '';
        if ($raw === '') {
            return 'download';
        }

        $storedExt = strtolower(pathinfo($raw, PATHINFO_EXTENSION) ?: 'bin');
        if (preg_match('/\.pd$/i', $raw)) {
            $storedExt = 'pdf';
        }

        $friendly = $this->getRawOriginal('original_filename')
            ?: $this->getRawOriginal('description');

        if ($friendly !== null && $friendly !== '') {
            $friendly = basename(str_replace(["\0", "\r", "\n"], '', $friendly));
            $stem = pathinfo($friendly, PATHINFO_FILENAME);
            $stem = trim($stem) !== '' ? $stem : 'document';
            $stem = preg_replace('/[^\pL\pN\s\-_().\[\]]+/u', '_', $stem);
            $stem = trim(preg_replace('/_+/', '_', $stem), '._ ');
            if ($stem === '') {
                $stem = 'document';
            }

            return Str::limit($stem, 180, '') . '.' . $storedExt;
        }

        $base = basename($raw);
        if (preg_match('/\.pd$/i', $base)) {
            return preg_replace('/\.pd$/i', '.pdf', $base);
        }

        return $base;
    }
}
