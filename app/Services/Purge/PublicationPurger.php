<?php

namespace App\Services\Purge;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Deletes one publication with every child row and stored file. Used by the
 * admin purge tool, hub:purge-user, and publications:purge-rejected, so the
 * list of children lives in exactly one place.
 */
class PublicationPurger
{
    /** Child tables keyed by their publication FK column. */
    private const CHILDREN = [
        'publication_attachments' => 'publication_id',
        'publication_tags' => 'publication_id',
        'publication_countries' => 'publication_id',
        'publication_views' => 'publication_id',
        'publication_approval_logs' => 'publication_id',
        'publication_comments' => 'publication_id',
        'publication_access_groups' => 'publication_id',
        'publication_community_of_practices' => 'publication_id',
        'favourites' => 'publication_id',
        'geographical_scope_publication' => 'publication_id',
        'publication_summaries' => 'resource_id',
    ];

    /**
     * @return array{files: list<string>, rows: array<string, int>} what was (or would be) removed
     */
    public function purge(int $publicationId, bool $dryRun = false): array
    {
        $files = $this->collectFiles($publicationId);
        $rows = [];

        foreach (self::CHILDREN as $table => $column) {
            $query = DB::table($table)->where($column, $publicationId);
            $rows[$table] = $query->count();
            if (! $dryRun && $rows[$table] > 0) {
                $query->delete();
            }
        }

        $rows['publication'] = DB::table('publication')->where('id', $publicationId)->count();
        if (! $dryRun) {
            foreach ($files as $file) {
                $this->deleteFile($file);
            }
            DB::table('publication')->where('id', $publicationId)->delete();
        }

        return ['files' => $files, 'rows' => array_filter($rows)];
    }

    /** @return list<string> absolute paths on the hub files disk */
    private function collectFiles(int $publicationId): array
    {
        $files = [];
        $base = hub_storage_path('uploads/publications');

        foreach (DB::table('publication_attachments')->where('publication_id', $publicationId)->pluck('file') as $file) {
            if ($file) {
                $files[] = $base.'/'.$file;
            }
        }
        foreach (DB::table('publication_summaries')->where('resource_id', $publicationId)->whereNotNull('file_path')->pluck('file_path') as $file) {
            if ($file) {
                $files[] = $base.'/summaries/'.$file;
            }
        }

        $cover = (string) (DB::table('publication')->where('id', $publicationId)->value('cover') ?? '');
        if ($cover !== '' && $cover !== 'cover.jpg' && ! preg_match('#^https?://#i', $cover)) {
            $files[] = $base.'/'.$cover;
        }

        return $files;
    }

    private function deleteFile(string $path): void
    {
        try {
            if (is_file($path)) {
                @unlink($path);
            }
        } catch (\Throwable $e) {
            Log::warning('PublicationPurger: could not delete file', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }
}
