<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\PublicationType;

/**
 * Maps uploaded publication files and URLs to rows in file_type (PublicationType).
 */
final class PublicationFileTypeResolver
{
    /** @var array<string, string> */
    private const MIME_TO_TOKEN = [
        'application/pdf' => 'pdf',
        'application/x-pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.ms-word' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
        'application/vnd.oasis.opendocument.presentation' => 'odp',
        'application/rtf' => 'rtf',
        'text/rtf' => 'rtf',
        'text/plain' => 'txt',
        'text/csv' => 'csv',
    ];

    public static function resolve(?string $absoluteFilePath = null, ?string $pubUrl = null): ?PublicationType
    {
        $tokens = self::candidateTokens($absoluteFilePath, $pubUrl);

        foreach ($tokens as $token) {
            $type = self::findTypeByToken($token);
            if ($type) {
                return $type;
            }
        }

        foreach ($tokens as $token) {
            if (str_contains($token, 'video')) {
                $type = PublicationType::where('name', 'like', '%video%')->first();
                if ($type) {
                    return $type;
                }
            }
        }

        return PublicationType::where('name', 'like', '%other%')->first();
    }

    /**
     * Best file type for UI badges: infer from attachments / external link when stored type is generic.
     */
    public static function resolveForPublication(?Publication $publication): ?PublicationType
    {
        if (! $publication) {
            return null;
        }

        if ((int) ($publication->is_video ?? 0) === 1) {
            $video = PublicationType::where('name', 'like', '%video%')->first();
            if ($video) {
                return $video;
            }
        }

        $stored = $publication->relationLoaded('file_type')
            ? $publication->file_type
            : $publication->file_type()->first();

        $inferred = self::inferFromPublicationSources($publication);
        if ($inferred && ! self::isGenericOtherType($inferred)) {
            return $inferred;
        }

        if ($stored && ! self::isGenericOtherType($stored)) {
            return $stored;
        }

        return $inferred ?? $stored ?? PublicationType::where('name', 'like', '%other%')->first();
    }

    /**
     * Icon + label metadata for publication card corner badges.
     *
     * @return array{icon: string, short: string, show_text_label: bool, title: string}
     */
    public static function badgePresentation(?PublicationType $type): array
    {
        $title = trim((string) ($type->name ?? ''));
        $t = strtolower($title);
        $icon = 'fa-file-o';
        $short = strtoupper(\Illuminate\Support\Str::limit($title, 7, ''));
        $showTextLabel = false;

        if (str_contains($t, 'pdf')) {
            $icon = 'fa-file-pdf';
        } elseif (str_contains($t, 'word') || str_contains($t, 'doc')) {
            $icon = 'fa-file-word';
        } elseif (str_contains($t, 'excel') || str_contains($t, 'xls')) {
            $icon = 'fa-file-excel';
        } elseif (str_contains($t, 'presentation') || str_contains($t, 'powerpoint') || str_contains($t, 'ppt')) {
            $icon = 'fa-file-powerpoint';
        } elseif (str_contains($t, 'url') && str_contains($t, 'link')) {
            $icon = 'fa-link';
        } elseif (str_contains($t, 'link')) {
            $icon = 'fa-link';
        } elseif (str_contains($t, 'video')) {
            $icon = 'fa-file-video';
        } elseif (str_contains($t, 'audio')) {
            $icon = 'fa-file-audio';
        } elseif (str_contains($t, 'image') || str_contains($t, 'photo')) {
            $icon = 'fa-file-image';
        } else {
            $showTextLabel = strlen($short) >= 2 && strlen($short) <= 10;
        }

        return [
            'icon' => $icon,
            'short' => $short,
            'show_text_label' => $showTextLabel,
            'title' => $title,
        ];
    }

    private static function inferFromPublicationSources(Publication $publication): ?PublicationType
    {
        $attachments = $publication->relationLoaded('attachments')
            ? $publication->attachments
            : $publication->attachments()->orderBy('id')->get();

        foreach ($attachments as $attachment) {
            $raw = (string) $attachment->getRawOriginal('file');
            if ($raw === '') {
                continue;
            }

            $absolute = function_exists('resolve_publication_upload_disk_path')
                ? resolve_publication_upload_disk_path($raw)
                : null;
            if ($absolute && is_file($absolute)) {
                $type = self::resolve($absolute, null);
                if ($type && ! self::isGenericOtherType($type)) {
                    return $type;
                }
            }

            $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
            if ($ext !== '') {
                $type = self::findTypeByToken($ext);
                if ($type && ! self::isGenericOtherType($type)) {
                    return $type;
                }
            }
        }

        $link = trim((string) $publication->getRawOriginal('publication'));
        if ($link !== '') {
            $type = self::resolve(null, $link);
            if ($type && ! self::isGenericOtherType($type)) {
                return $type;
            }
        }

        return null;
    }

    public static function isGenericOtherType(?PublicationType $type): bool
    {
        if (! $type) {
            return true;
        }

        $name = strtolower(trim((string) $type->name));

        return $name === '' || $name === 'other' || str_contains($name, 'other');
    }

    /**
     * @return list<string>
     */
    public static function candidateTokens(?string $absoluteFilePath, ?string $pubUrl): array
    {
        $tokens = [];

        if ($absoluteFilePath && is_string($absoluteFilePath) && is_file($absoluteFilePath)) {
            $ext = strtolower(pathinfo($absoluteFilePath, PATHINFO_EXTENSION));
            if ($ext !== '') {
                $tokens[] = $ext;
            }

            if (function_exists('getFileMimeType')) {
                $mime = strtolower(trim((string) getFileMimeType($absoluteFilePath)));
                if ($mime !== '' && $mime !== 'file not found') {
                    $fromMime = self::mimeToToken($mime);
                    if ($fromMime) {
                        $tokens[] = $fromMime;
                    }
                    $tokens[] = $mime;
                }
            }
        }

        if ($pubUrl && is_string($pubUrl)) {
            $url = strtolower(trim($pubUrl));
            if (function_exists('is_video_platform_url') && is_video_platform_url($url)) {
                $tokens[] = 'video';
            } else {
                $path = parse_url($url, PHP_URL_PATH) ?: $url;
                $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
                if ($ext !== '') {
                    $tokens[] = $ext;
                }
            }
        }

        $unique = [];
        foreach ($tokens as $token) {
            $token = strtolower(trim($token));
            if ($token !== '' && ! in_array($token, $unique, true)) {
                $unique[] = $token;
            }
        }

        return $unique;
    }

    public static function mimeToToken(string $mime): ?string
    {
        $mime = strtolower(trim($mime));

        if (isset(self::MIME_TO_TOKEN[$mime])) {
            return self::MIME_TO_TOKEN[$mime];
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        return null;
    }

    public static function findTypeByToken(string $token): ?PublicationType
    {
        $token = strtolower(trim($token));
        if ($token === '') {
            return null;
        }

        $types = PublicationType::query()
            ->whereNotNull('mime_types')
            ->where('mime_types', '!=', '')
            ->get();

        foreach ($types as $type) {
            if (self::mimeTypesFieldContainsToken((string) $type->mime_types, $token)) {
                return $type;
            }
        }

        // Legacy partial match (e.g. youtube in video mime_types blob)
        return PublicationType::where('mime_types', 'like', '%'.$token.'%')->first();
    }

    private static function mimeTypesFieldContainsToken(string $mimeTypesField, string $token): bool
    {
        $parts = preg_split('/[\s,;]+/', strtolower($mimeTypesField), -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($parts)) {
            return false;
        }

        return in_array($token, $parts, true);
    }
}
