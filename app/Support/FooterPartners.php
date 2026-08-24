<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class FooterPartners
{
    public const MAX_ITEMS = 12;

    /**
     * @param  mixed  $settings
     */
    public static function showNames($settings): bool
    {
        if (! is_object($settings) || ! isset($settings->show_partner_names)) {
            return false;
        }

        return filter_var($settings->show_partner_names, FILTER_VALIDATE_BOOLEAN);
    }

    public const LOGO_MAX_HEIGHT_DEFAULT = 100;
    public const LOGO_MAX_HEIGHT_MIN = 50;
    public const LOGO_MAX_HEIGHT_MAX = 200;

    /**
     * @param  mixed  $settings
     */
    public static function logoMaxHeight($settings): int
    {
        $value = self::LOGO_MAX_HEIGHT_DEFAULT;
        if (is_object($settings) && isset($settings->partner_logo_max_height) && $settings->partner_logo_max_height !== '') {
            $value = (int) $settings->partner_logo_max_height;
        }

        return max(self::LOGO_MAX_HEIGHT_MIN, min(self::LOGO_MAX_HEIGHT_MAX, $value));
    }

    /**
     * @param  mixed  $raw
     * @return list<array{file: string, name: string, url: string, image: string}>
     */
    public static function items($raw): array
    {
        $decoded = self::decode($raw);
        $items = [];

        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $file = trim((string) ($row['file'] ?? $row['logo'] ?? ''));
            if ($file === '') {
                continue;
            }
            $image = function_exists('branding_image_url')
                ? branding_image_url($file)
                : $file;
            if ($image === '') {
                continue;
            }
            $items[] = [
                'file' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                'name' => trim((string) ($row['name'] ?? '')),
                'url' => self::safeUrl((string) ($row['url'] ?? '')),
                'image' => $image,
            ];
        }

        return $items;
    }

    /**
     * @return list<array{file: string, name: string, url: string}>
     */
    public static function fromRequest(Request $request, callable $storeUpload): array
    {
        $rows = $request->input('partner_logos', []);
        if (! is_array($rows)) {
            return [];
        }

        $saved = [];
        foreach ($rows as $index => $row) {
            if (count($saved) >= self::MAX_ITEMS) {
                break;
            }
            if (! is_array($row)) {
                continue;
            }

            $file = trim((string) ($row['file'] ?? ''));
            $upload = $request->file('partner_logos.'.$index.'.image');
            if ($upload instanceof UploadedFile && $upload->isValid()) {
                $stored = $storeUpload($upload);
                if (is_string($stored) && $stored !== '') {
                    $file = $stored;
                }
            }

            if ($file === '') {
                continue;
            }

            $saved[] = [
                'file' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                'name' => mb_substr(trim((string) ($row['name'] ?? '')), 0, 120),
                'url' => self::safeUrl((string) ($row['url'] ?? '')),
            ];
        }

        return $saved;
    }

    /**
     * @param  mixed  $raw
     * @return list<array<string, mixed>>
     */
    public static function decode($raw): array
    {
        if (is_array($raw)) {
            return array_values($raw);
        }
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    private static function safeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (! preg_match('#^https?://#i', $url)) {
            return '';
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }
}
