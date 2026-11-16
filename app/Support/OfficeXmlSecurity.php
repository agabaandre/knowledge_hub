<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * XXE guard for zip-based office documents (OOXML and OpenDocument).
 * Legitimate Office/LibreOffice output never contains a DTD, so any XML part
 * carrying <!DOCTYPE or <!ENTITY is rejected before a parser or LibreOffice
 * ever sees it (CVE-class: file:///etc/passwd exfiltration via word/document.xml).
 */
final class OfficeXmlSecurity
{
    /** @var list<string> */
    public const ZIP_OFFICE_EXTENSIONS = ['docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp'];

    private const MAX_PART_SCAN_BYTES = 2097152; // 2 MB per XML part is ample for the prolog

    public static function appliesTo(string $extension): bool
    {
        return in_array(strtolower($extension), self::ZIP_OFFICE_EXTENSIONS, true);
    }

    /**
     * True when the document is safe to parse. Unreadable or corrupt archives
     * are unsafe by definition (fail closed).
     */
    public static function isSafe(string $absolutePath, ?string $extension = null): bool
    {
        $extension = strtolower($extension ?? pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (! self::appliesTo($extension)) {
            return true;
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath, ZipArchive::RDONLY) !== true) {
            Log::warning('OfficeXmlSecurity: unreadable archive rejected', ['path' => basename($absolutePath)]);

            return false;
        }

        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = (string) $zip->getNameIndex($i);
                if (! preg_match('/\.(xml|rels)$/i', $name)) {
                    continue;
                }

                $content = $zip->getFromIndex($i, self::MAX_PART_SCAN_BYTES);
                if ($content === false) {
                    return false;
                }

                if (stripos($content, '<!DOCTYPE') !== false || stripos($content, '<!ENTITY') !== false) {
                    Log::warning('OfficeXmlSecurity: DTD/entity declaration rejected (possible XXE)', [
                        'path' => basename($absolutePath),
                        'part' => $name,
                        'ip' => function_exists('request') ? request()->ip() : null,
                    ]);

                    return false;
                }
            }
        } finally {
            $zip->close();
        }

        return true;
    }
}
