<?php

namespace App\Services;

use App\Models\CustomAttachment;
use App\Models\Publication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HubStoragePublicationIndexService
{
    private const CACHE_KEY = 'hub_storage_publication_file_index';

    private const CACHE_TTL = 300;

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function index(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->buildIndex();
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function referencesForStoragePath(string $storagePath): array
    {
        $storagePath = $this->normalizePath($storagePath);
        if ($storagePath === '') {
            return [];
        }

        $index = $this->index();
        $refs = [];

        foreach ($this->filenameVariants($storagePath) as $variant) {
            if (isset($index[$variant])) {
                $refs = array_merge($refs, $index[$variant]);
            }
        }

        return $this->dedupeRefs($refs);
    }

    /**
     * Map basenames in a directory to linked publications/resources.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function referencesInDirectory(string $directory): array
    {
        $directory = trim($this->normalizePath($directory), '/');
        $index = $this->index();
        $out = [];

        foreach ($index as $path => $refs) {
            $parent = dirname($path);
            if ($parent === '.') {
                $parent = '';
            }

            if ($parent !== $directory) {
                continue;
            }

            $basename = basename($path);
            $out[$basename] = $this->dedupeRefs(array_merge($out[$basename] ?? [], $refs));
        }

        return $out;
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildIndex(): array
    {
        $index = [];

        if (Schema::hasTable('publication')) {
            Publication::query()
                ->select(['id', 'title', 'publication', 'cover'])
                ->whereNotNull('title')
                ->orderBy('id')
                ->chunkById(500, function ($rows) use (&$index) {
                    foreach ($rows as $publication) {
                        $editUrl = url('/admin/publications/edit?id='.$publication->id);
                        $title = (string) $publication->title;

                        $mainFile = $publication->getRawOriginal('publication');
                        if (is_string($mainFile) && $mainFile !== '') {
                            $this->addRef($index, $mainFile, 'uploads/publications', [
                                'id' => $publication->id,
                                'title' => $title,
                                'role' => 'main_file',
                                'edit_url' => $editUrl,
                            ]);
                        }

                        $cover = $publication->getRawOriginal('cover');
                        if (is_string($cover) && $cover !== '') {
                            $this->addRef($index, $cover, 'uploads/publications', [
                                'id' => $publication->id,
                                'title' => $title,
                                'role' => 'cover',
                                'edit_url' => $editUrl,
                            ]);
                        }
                    }
                });
        }

        if (Schema::hasTable('publication_attachments') && Schema::hasTable('publication')) {
            DB::table('publication_attachments')
                ->join('publication', 'publication.id', '=', 'publication_attachments.publication_id')
                ->select([
                    'publication_attachments.file',
                    'publication_attachments.original_filename',
                    'publication.id as publication_id',
                    'publication.title',
                ])
                ->whereNotNull('publication_attachments.file')
                ->where('publication_attachments.file', '!=', '')
                ->orderBy('publication_attachments.id')
                ->lazy()
                ->each(function ($row) use (&$index) {
                    $label = $row->title ?: ($row->original_filename ?: 'Publication #'.$row->publication_id);
                    $this->addRef($index, (string) $row->file, 'uploads/publications', [
                        'id' => (int) $row->publication_id,
                        'title' => (string) $label,
                        'role' => 'attachment',
                        'edit_url' => url('/admin/publications/edit?id='.$row->publication_id),
                    ]);
                });
        }

        if (Schema::hasTable('publication_summaries') && Schema::hasTable('publication')) {
            DB::table('publication_summaries')
                ->join('publication', 'publication.id', '=', 'publication_summaries.publication_id')
                ->select([
                    'publication_summaries.file_path',
                    'publication.id as publication_id',
                    'publication.title',
                ])
                ->whereNotNull('publication_summaries.file_path')
                ->where('publication_summaries.file_path', '!=', '')
                ->lazy()
                ->each(function ($row) use (&$index) {
                    $this->addRef($index, (string) $row->file_path, 'uploads/publications/summaries', [
                        'id' => (int) $row->publication_id,
                        'title' => (string) $row->title,
                        'role' => 'summary',
                        'edit_url' => url('/admin/publications/edit?id='.$row->publication_id),
                    ]);
                });
        }

        if (Schema::hasTable('custom_attachments')) {
            CustomAttachment::query()
                ->select(['record_id', 'model', 'path', 'name'])
                ->whereNotNull('path')
                ->where('path', '!=', '')
                ->lazy()
                ->each(function (CustomAttachment $attachment) use (&$index) {
                    $model = (string) $attachment->model;
                    $prefix = str_contains(strtolower($model), 'forum')
                        ? 'uploads/forums'
                        : 'uploads/forum';

                    $this->addRef($index, (string) $attachment->getRawOriginal('path'), $prefix, [
                        'id' => (int) $attachment->record_id,
                        'title' => (string) ($attachment->name ?: 'Forum attachment'),
                        'role' => 'forum_attachment',
                        'edit_url' => url('/admin/forums/details?id='.$attachment->record_id),
                    ]);
                });
        }

        return $index;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $index
     * @param  array<string, mixed>  $ref
     */
    private function addRef(array &$index, string $raw, string $defaultPrefix, array $ref): void
    {
        $storagePath = $this->normalizeStoredFile($raw, $defaultPrefix);
        if ($storagePath === '') {
            return;
        }

        foreach ($this->filenameVariants($storagePath) as $variant) {
            $index[$variant][] = $ref;
        }
    }

    private function normalizeStoredFile(string $raw, string $defaultPrefix): string
    {
        $raw = trim(str_replace('\\', '/', $raw));
        if ($raw === '' || preg_match('#^https?://#i', $raw)) {
            return '';
        }

        if (preg_match('#^uploads/(https?://)#i', $raw)) {
            return '';
        }

        if (str_starts_with($raw, 'uploads/')) {
            return $this->normalizePath($raw);
        }

        return $this->normalizePath(rtrim($defaultPrefix, '/').'/'.basename($raw));
    }

    private function normalizePath(string $path): string
    {
        return trim(str_replace('\\', '/', $path), '/');
    }

    /**
     * @return array<int, string>
     */
    private function filenameVariants(string $path): array
    {
        $path = $this->normalizePath($path);
        if ($path === '') {
            return [];
        }

        $variants = [$path];
        if (preg_match('/\.pd$/i', $path)) {
            $variants[] = preg_replace('/\.pd$/i', '.pdf', $path);
        }
        if (preg_match('/\.pdf$/i', $path)) {
            $variants[] = preg_replace('/\.pdf$/i', '.pd', $path);
        }

        return array_values(array_unique($variants));
    }

    /**
     * @param  array<int, array<string, mixed>>  $refs
     * @return array<int, array<string, mixed>>
     */
    private function dedupeRefs(array $refs): array
    {
        $seen = [];
        $out = [];

        foreach ($refs as $ref) {
            $key = ($ref['id'] ?? 0).'-'.($ref['role'] ?? '');
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $ref;
        }

        return $out;
    }
}
