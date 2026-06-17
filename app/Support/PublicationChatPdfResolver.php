<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\PublicationAttachment;

/**
 * Resolves PDF source keys (main / attachment id) for Khub AI ChatPDF uploads.
 */
final class PublicationChatPdfResolver
{
    public static function sourceKey(array $source): string
    {
        $attachmentId = $source['attachment_id'] ?? null;

        return ($attachmentId === null || $attachmentId === '') ? 'main' : (string) $attachmentId;
    }

    /**
     * @return list<string>
     */
    public static function defaultSelectedKeys(Publication $publication): array
    {
        $sources = $publication->pdf_sources;
        if ($sources === []) {
            return [];
        }

        return [self::sourceKey($sources[0])];
    }

    /**
     * @param  list<string|int|null>|null  $requestedKeys
     * @return list<string>
     */
    public static function normalizeSelectedKeys(Publication $publication, ?array $requestedKeys): array
    {
        $allowed = [];
        foreach ($publication->pdf_sources as $source) {
            $allowed[self::sourceKey($source)] = true;
        }

        if ($allowed === []) {
            return [];
        }

        if ($requestedKeys === null || $requestedKeys === []) {
            return self::defaultSelectedKeys($publication);
        }

        $valid = [];
        foreach ($requestedKeys as $key) {
            $normalized = self::normalizeKey($key);
            if ($normalized !== null && isset($allowed[$normalized])) {
                $valid[] = $normalized;
            }
        }

        return $valid !== [] ? array_values(array_unique($valid)) : self::defaultSelectedKeys($publication);
    }

    /**
     * @param  list<string>  $keys
     */
    public static function selectionKey(array $keys): string
    {
        $sorted = $keys;
        sort($sorted, SORT_STRING);

        return implode(',', $sorted);
    }

    /**
     * @return array{url: string|null, path: string|null, label: string}|null
     */
    public static function resolvePdfForKey(Publication $publication, string $key): ?array
    {
        foreach ($publication->pdf_sources as $source) {
            if (self::sourceKey($source) !== $key) {
                continue;
            }

            if (($source['type'] ?? '') === 'main') {
                return [
                    'url' => $publication->publication_pdf_url,
                    'path' => $publication->publication_pdf_path,
                    'label' => (string) ($source['label'] ?? 'Main document'),
                ];
            }

            $attachmentId = (int) ($source['attachment_id'] ?? 0);
            if ($attachmentId <= 0) {
                return null;
            }

            $attachment = $publication->attachments->firstWhere('id', $attachmentId);
            if (! $attachment instanceof PublicationAttachment || ! $attachment->is_pdf) {
                return null;
            }

            return [
                'url' => $attachment->file_url ?? null,
                'path' => $attachment->file_path ?? null,
                'label' => (string) ($source['label'] ?? 'Document'),
            ];
        }

        return null;
    }

    /**
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    public static function labelsForKeys(Publication $publication, array $keys): array
    {
        $labels = [];
        foreach ($keys as $key) {
            $pdf = self::resolvePdfForKey($publication, $key);
            if ($pdf !== null) {
                $labels[$key] = $pdf['label'];
            }
        }

        return $labels;
    }

    public static function primaryAttachmentId(array $selectedKeys): ?int
    {
        foreach ($selectedKeys as $key) {
            if ($key === 'main') {
                return null;
            }
            $id = (int) $key;
            if ($id > 0) {
                return $id;
            }
        }

        return null;
    }

    private static function normalizeKey(mixed $key): ?string
    {
        if ($key === null || $key === '') {
            return 'main';
        }

        $stringKey = is_string($key) ? trim($key) : (string) $key;
        if ($stringKey === '' || $stringKey === 'main') {
            return 'main';
        }

        if (ctype_digit($stringKey)) {
            return $stringKey;
        }

        return null;
    }
}
