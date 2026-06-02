<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Allow-list for publication wizard attachments (web + API).
 * Blocks executables, scripts, HTML, and other high-risk uploads.
 */
final class PublicationAttachmentSecurity
{
    /** @var list<string> */
    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff',
        'pdf',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf',
        'txt', 'csv',
        'mp3', 'wav', 'ogg', 'oga', 'opus', 'flac', 'm4a', 'aac', 'wma',
        'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
    ];

    /** @var list<string> */
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'phps',
        'js', 'mjs', 'cjs', 'jsx', 'ts', 'tsx',
        'exe', 'bat', 'cmd', 'com', 'scr', 'msi', 'dll', 'sys', 'lnk',
        'sh', 'bash', 'zsh', 'fish',
        'py', 'pyc', 'pyw', 'rb', 'pl', 'pm', 'jar', 'war', 'class',
        'asp', 'aspx', 'ashx', 'asmx', 'jsp', 'jspx',
        'cgi', 'htaccess', 'htpasswd',
        'vbs', 'vbe', 'ps1', 'psm1', 'psd1',
        'html', 'htm', 'xhtml', 'shtml', 'svg',
        'swf', 'hta', 'cpl', 'msc', 'gadget',
        'app', 'deb', 'rpm', 'dmg', 'iso', 'img', 'bin', 'run',
        'sql', 'reg', 'inf', 'ws', 'wsf', 'wsc', 'wsh', 'cab',
    ];

    /** @var list<string> */
    private const BLOCKED_MIME_PREFIXES = [
        'application/x-php',
        'application/php',
        'text/php',
        'text/x-php',
        'application/javascript',
        'text/javascript',
        'application/x-javascript',
        'application/x-msdownload',
        'application/x-msdos-program',
        'application/x-executable',
        'application/x-sh',
        'application/x-shellscript',
        'text/html',
        'image/svg+xml',
    ];

    public static function htmlAcceptAttribute(): string
    {
        $parts = [
            '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
            '.odt', '.ods', '.odp', '.rtf', '.txt', '.csv',
            '.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.tif', '.tiff',
            '.mp3', '.wav', '.ogg', '.m4a', '.aac', '.flac', '.wma',
            '.mp4', '.m4v', '.mov', '.avi', '.webm', '.mkv', '.wmv', '.flv', '.3gp', '.mpeg', '.mpg',
            'application/pdf',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/tiff',
            'audio/*', 'video/*',
        ];

        return implode(',', $parts);
    }

    public static function rejectionMessage(?string $filename = null): string
    {
        $base = 'This file type is not allowed. Please upload documents (PDF, Office), images, audio, or video only. Scripts and executables (e.g. .php, .js, .exe) are blocked.';

        if ($filename) {
            return $base.' ('.basename($filename).')';
        }

        return $base;
    }

    /**
     * @return list<string>
     */
    public static function blockedExtensionsForJs(): array
    {
        return self::BLOCKED_EXTENSIONS;
    }

    /**
     * @return list<string>
     */
    public static function allowedExtensionsForJs(): array
    {
        return self::ALLOWED_EXTENSIONS;
    }

    public static function isAllowedUpload(UploadedFile $file): bool
    {
        $name = (string) $file->getClientOriginalName();
        if (self::isBlockedFilename($name)) {
            return false;
        }

        $ext = self::extensionFromFilename($name);
        if ($ext === '' || ! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return false;
        }

        $mime = strtolower((string) $file->getMimeType());
        if ($mime !== '' && self::isBlockedMime($mime)) {
            return false;
        }

        return self::mimeMatchesExtension($mime, $ext);
    }

    public static function isBlockedFilename(string $filename): bool
    {
        $lower = strtolower(basename($filename));

        if ($lower === '' || str_contains($lower, "\0")) {
            return true;
        }

        if (preg_match('/\.(php\d*|phtml|phar|phps)(\.|$)/i', $lower)) {
            return true;
        }

        foreach (self::BLOCKED_EXTENSIONS as $blocked) {
            if (preg_match('/\.'.preg_quote($blocked, '/').'(\.|$)/i', $lower)) {
                return true;
            }
        }

        return false;
    }

    private static function extensionFromFilename(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return preg_replace('/[^a-z0-9]/', '', $ext);
    }

    private static function isBlockedMime(string $mime): bool
    {
        foreach (self::BLOCKED_MIME_PREFIXES as $blocked) {
            if ($mime === $blocked || str_starts_with($mime, $blocked)) {
                return true;
            }
        }

        return false;
    }

    private static function mimeMatchesExtension(string $mime, string $ext): bool
    {
        if ($mime === '' || $mime === 'application/octet-stream') {
            return true;
        }

        if (str_starts_with($mime, 'image/') && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff'], true)) {
            return $mime !== 'image/svg+xml';
        }

        if (str_starts_with($mime, 'audio/') && in_array($ext, ['mp3', 'wav', 'ogg', 'oga', 'opus', 'flac', 'm4a', 'aac', 'wma'], true)) {
            return true;
        }

        if (str_starts_with($mime, 'video/') && in_array($ext, ['mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg'], true)) {
            return true;
        }

        $map = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'ppt' => ['application/vnd.ms-powerpoint'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'odt' => ['application/vnd.oasis.opendocument.text'],
            'ods' => ['application/vnd.oasis.opendocument.spreadsheet'],
            'odp' => ['application/vnd.oasis.opendocument.presentation'],
            'rtf' => ['application/rtf', 'text/rtf'],
            'txt' => ['text/plain'],
            'csv' => ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'],
        ];

        if (! isset($map[$ext])) {
            return true;
        }

        return in_array($mime, $map[$ext], true);
    }
}
