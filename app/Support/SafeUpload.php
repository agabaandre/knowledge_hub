<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Central upload helper: content-derived extensions only, never trust the client name.
 */
final class SafeUpload
{
    /** @var list<string> */
    public const IMAGES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** @var list<string> */
    public const DOCUMENTS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'odt', 'ods', 'odp', 'rtf', 'txt', 'csv',
    ];

    /** @var list<string> */
    public const MEDIA = [
        'mp3', 'wav', 'ogg', 'oga', 'opus', 'flac', 'm4a', 'aac', 'wma',
        'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
    ];

    /** Ambiguous MIME types where a client extension may be used (never for images). */
    private const FALLBACK_MIME_PREFIXES = [
        'application/zip',
        'application/octet-stream',
        'application/x-ole-storage',
        'application/cdfv2',
        'application/vnd.ms-office',
        'text/plain',
    ];

    private const MAX_SCAN_BYTES = 26214400; // 25 MB

    /**
     * @param  list<string>  $allowed
     */
    public static function extension(UploadedFile $file, array $allowed, string $field = 'file'): string
    {
        $allowed = array_values(array_unique(array_map(
            static fn ($ext) => self::normalizeExtension((string) $ext),
            $allowed
        )));

        $name = (string) $file->getClientOriginalName();
        self::assertSafeFilename($name, $field);
        self::assertNoDangerousPayload($file, $field);
        self::assertSafeMime($file, $field);

        $contentExt = self::normalizeExtension((string) ($file->guessExtension() ?: ''));
        $blockedName = PublicationAttachmentSecurity::isBlockedFilename($name);

        if ($blockedName) {
            // Allow remapping only for a single blocked extension on a real image (e.g. photo.php7 → .png).
            // Reject double extensions like a.php.jpg and non-image blocked names (.htaccess, shell.php).
            if (
                self::allowsImageRemapDespiteBlockedName($name)
                && $contentExt !== ''
                && in_array($contentExt, self::IMAGES, true)
                && in_array($contentExt, $allowed, true)
            ) {
                Log::warning('Upload remapped blocked client extension to content type', [
                    'ip' => request()->ip(),
                    'user_id' => optional(auth()->user())->id,
                    'field' => $field,
                    'client_name' => $name,
                    'extension' => $contentExt,
                ]);

                return $contentExt;
            }

            self::reject($field, 'This file type is not allowed.', $name);
        }

        if ($contentExt !== '' && in_array($contentExt, $allowed, true)) {
            return $contentExt;
        }

        // Never fall back to the client extension for images.
        if (self::isImageOnlyAllowlist($allowed)) {
            self::reject($field, 'The uploaded image type is not allowed.', $name);
        }

        $mime = strtolower((string) $file->getMimeType());
        if (! self::mimeAllowsClientFallback($mime)) {
            self::reject($field, 'Unable to determine a safe file type for this upload.', $name);
        }

        $clientExt = self::normalizeExtension((string) $file->getClientOriginalExtension());
        if ($clientExt === '' || ! in_array($clientExt, $allowed, true)) {
            self::reject($field, 'This file type is not allowed.', $name);
        }

        // Client fallback must never produce an image extension when content did not.
        if (in_array($clientExt, self::IMAGES, true)) {
            self::reject($field, 'The uploaded image type is not allowed.', $name);
        }

        return $clientExt;
    }

    /**
     * @param  list<string>  $allowed
     */
    public static function move(
        UploadedFile $file,
        string $dir,
        array $allowed,
        string $field = 'file',
        ?string $basename = null
    ): string {
        $ext = self::extension($file, $allowed, $field);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($basename !== null && $basename !== '') {
            $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename) ?? '';
            $safeBase = trim($safeBase, '._-');
            $safeBase = pathinfo($safeBase, PATHINFO_FILENAME) ?: $safeBase;
            if ($safeBase === '') {
                $safeBase = md5_file($file->getRealPath()) ?: uniqid('upload_', true);
            }
            $filename = $safeBase.'.'.$ext;
        } else {
            $hash = md5_file($file->getRealPath());
            if ($hash === false) {
                self::reject($field, 'Unable to process the uploaded file.', (string) $file->getClientOriginalName());
            }
            $filename = $hash.'.'.$ext;
        }

        $file->move($dir, $filename);

        return $filename;
    }

    /**
     * @param  list<string>  $allowed
     */
    public static function isAllowed(UploadedFile $file, array $allowed): bool
    {
        try {
            self::extension($file, $allowed);

            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    /**
     * Comma-separated list for Laravel mimes: rules.
     *
     * @param  list<string>  ...$groups
     */
    public static function mimesRule(array ...$groups): string
    {
        $exts = [];
        foreach ($groups as $group) {
            foreach ($group as $ext) {
                $exts[] = self::normalizeExtension((string) $ext);
            }
        }

        return implode(',', array_values(array_unique(array_filter($exts))));
    }

    private static function assertSafeFilename(string $name, string $field): void
    {
        $base = basename($name);
        if ($base === '' || str_ends_with($base, '.')) {
            self::reject($field, 'This file type is not allowed.', $name);
        }

        if (str_contains($name, '%00') || str_contains($name, "\0")) {
            self::reject($field, 'This file type is not allowed.', $name);
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $name)) {
            self::reject($field, 'This file type is not allowed.', $name);
        }
    }

    private static function assertNoDangerousPayload(UploadedFile $file, string $field): void
    {
        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            self::reject($field, 'Unable to process the uploaded file.', (string) $file->getClientOriginalName());
        }

        $needles = ['<?php', '<?=', '<!--#exec', '<!--#include', '<!--#echo', '<!--#config'];
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            self::reject($field, 'Unable to process the uploaded file.', (string) $file->getClientOriginalName());
        }

        $scanned = 0;
        $carry = '';
        try {
            while (! feof($handle) && $scanned < self::MAX_SCAN_BYTES) {
                $chunk = fread($handle, 8192);
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $scanned += strlen($chunk);
                $haystack = strtolower($carry.$chunk);
                foreach ($needles as $needle) {
                    if (str_contains($haystack, strtolower($needle))) {
                        self::reject($field, 'This file contains disallowed executable content.', (string) $file->getClientOriginalName());
                    }
                }
                $carry = substr($haystack, -16);
            }
        } finally {
            fclose($handle);
        }
    }

    private static function assertSafeMime(UploadedFile $file, string $field): void
    {
        $mime = strtolower((string) $file->getMimeType());
        if ($mime === '') {
            return;
        }

        $blockedFragments = ['svg', 'php', 'html', 'javascript', 'x-sh', 'executable'];
        foreach ($blockedFragments as $fragment) {
            if (str_contains($mime, $fragment)) {
                self::reject($field, 'This file type is not allowed.', (string) $file->getClientOriginalName());
            }
        }
    }

    private static function mimeAllowsClientFallback(string $mime): bool
    {
        if ($mime === '') {
            return true;
        }

        foreach (self::FALLBACK_MIME_PREFIXES as $prefix) {
            if ($mime === $prefix || str_starts_with($mime, $prefix)) {
                return true;
            }
        }

        if (str_starts_with($mime, 'audio/') || str_starts_with($mime, 'video/')) {
            return true;
        }

        // Office Open XML / OLE often reported as zip or ms-office variants.
        if (str_contains($mime, 'officedocument') || str_contains($mime, 'ms-excel')
            || str_contains($mime, 'msword') || str_contains($mime, 'ms-powerpoint')
            || str_contains($mime, 'opendocument')) {
            return true;
        }

        return false;
    }

    /**
     * @param  list<string>  $allowed
     */
    private static function isImageOnlyAllowlist(array $allowed): bool
    {
        if ($allowed === []) {
            return false;
        }

        foreach ($allowed as $ext) {
            if (! in_array($ext, self::IMAGES, true)) {
                return false;
            }
        }

        return true;
    }

    private static function normalizeExtension(string $ext): string
    {
        $ext = strtolower(preg_replace('/[^a-z0-9]/', '', $ext) ?? '');
        if ($ext === 'jpeg') {
            return 'jpg';
        }

        return $ext;
    }

    /**
     * True when the client name has a single blocked extension (x.php7), not a.php.jpg.
     */
    private static function allowsImageRemapDespiteBlockedName(string $name): bool
    {
        $base = strtolower(basename($name));

        if (preg_match('/\.(php\d*|phtml|phar|phps|pht|inc|shtml)\./i', $base)) {
            return false;
        }

        return (bool) preg_match('/\.(php\d*|phtml|phar|phps|pht|inc|shtml)$/i', $base);
    }

    private static function reject(string $field, string $message, string $filename): void
    {
        Log::warning('Unsafe upload rejected', [
            'ip' => request()->ip(),
            'user_id' => optional(auth()->user())->id,
            'field' => $field,
            'filename' => $filename,
            'message' => $message,
        ]);

        throw ValidationException::withMessages([
            $field => [$message],
        ]);
    }
}
