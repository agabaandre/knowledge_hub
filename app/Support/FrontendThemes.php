<?php

namespace App\Support;

use InvalidArgumentException;
use ZipArchive;

class FrontendThemes
{
    public const ID_UNIVERSITY = 'university';

    public const ID_LANGUAGE_ACADEMY = 'language-academy';

    public const ID_ONLINE_COURSE = 'online-course';

    public const DEFAULT = self::ID_UNIVERSITY;

    public const BUILTIN_IDS = [
        self::ID_UNIVERSITY,
        self::ID_LANGUAGE_ACADEMY,
        self::ID_ONLINE_COURSE,
    ];

    /**
     * @return list<array{id: string, name: string, extends: string, source: string}>
     */
    public static function builtinCatalog(): array
    {
        return [
            ['id' => self::ID_UNIVERSITY, 'name' => 'University', 'extends' => self::ID_UNIVERSITY, 'source' => 'builtin'],
            ['id' => self::ID_LANGUAGE_ACADEMY, 'name' => 'Language Academy', 'extends' => self::ID_LANGUAGE_ACADEMY, 'source' => 'builtin'],
            ['id' => self::ID_ONLINE_COURSE, 'name' => 'Online Course', 'extends' => self::ID_ONLINE_COURSE, 'source' => 'builtin'],
        ];
    }

    public static function isBuiltin(string $id): bool
    {
        return in_array($id, self::BUILTIN_IDS, true);
    }

    public static function sanitizeId(?string $id): string
    {
        $id = strtolower(trim((string) $id));
        if ($id === '' || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id)) {
            return self::DEFAULT;
        }

        return $id;
    }

    /**
     * @return array{id: string, name: string, extends: string, tokens: array<string, string>}
     */
    public static function parseManifest(string $json): array
    {
        $data = json_decode($json, true);
        if (! is_array($data)) {
            throw new InvalidArgumentException('theme.json must be valid JSON.');
        }

        $name = trim((string) ($data['name'] ?? ''));
        $extends = self::sanitizeId((string) ($data['extends'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('theme.json needs a name.');
        }
        if (! self::isBuiltin($extends)) {
            throw new InvalidArgumentException('theme.json extends must be university, language-academy, or online-course.');
        }

        $id = self::sanitizeId((string) ($data['id'] ?? $name));
        $tokens = [];
        if (isset($data['tokens']) && is_array($data['tokens'])) {
            foreach ($data['tokens'] as $key => $value) {
                if (! is_string($key) || ! is_string($value)) {
                    continue;
                }
                $tokenKey = preg_replace('/[^a-z0-9\-]/', '', strtolower($key)) ?: '';
                $tokenValue = trim($value);
                if ($tokenKey === '' || $tokenValue === '' || strlen($tokenValue) > 64) {
                    continue;
                }
                $tokens[$tokenKey] = $tokenValue;
            }
        }

        return [
            'id' => $id,
            'name' => $name,
            'extends' => $extends,
            'tokens' => $tokens,
        ];
    }

    /**
     * @param  mixed  $raw
     * @return list<array<string, mixed>>
     */
    public static function packsFromSettings($raw): array
    {
        if (is_object($raw) && isset($raw->frontend_theme_packs)) {
            $raw = $raw->frontend_theme_packs;
        }
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($raw)) {
            return [];
        }

        $packs = [];
        foreach ($raw as $row) {
            if (! is_array($row) || empty($row['id'])) {
                continue;
            }
            $id = self::sanitizeId((string) $row['id']);
            if (self::isBuiltin($id)) {
                continue;
            }
            $extends = self::sanitizeId((string) ($row['extends'] ?? self::DEFAULT));
            if (! self::isBuiltin($extends)) {
                $extends = self::DEFAULT;
            }
            $packs[] = [
                'id' => $id,
                'name' => trim((string) ($row['name'] ?? $id)),
                'extends' => $extends,
                'css_url' => trim((string) ($row['css_url'] ?? '')),
                'source' => 'upload',
            ];
        }

        return $packs;
    }

    /**
     * @param  mixed  $settings
     * @return array{id: string, name: string, extends: string, source: string, css_url: string, tokens: array<string, string>}
     */
    public static function resolve($settings): array
    {
        $requested = self::sanitizeId(is_object($settings) ? ($settings->frontend_theme ?? null) : null);
        foreach (self::builtinCatalog() as $theme) {
            if ($theme['id'] === $requested) {
                return $theme + ['css_url' => '', 'tokens' => []];
            }
        }
        foreach (self::packsFromSettings($settings) as $pack) {
            if ($pack['id'] === $requested) {
                return $pack + ['tokens' => []];
            }
        }

        $fallback = self::builtinCatalog()[0];

        return $fallback + ['css_url' => '', 'tokens' => []];
    }

    /**
     * @return array{id: string, name: string, extends: string, tokens: array<string, string>}
     */
    public static function extractPack(string $zipPath, string $destDir): array
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new InvalidArgumentException('Could not open theme pack ZIP.');
        }

        $allowed = ['json', 'css', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'woff', 'woff2'];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = str_replace('\\', '/', (string) $zip->getNameIndex($i));
            if ($name === '' || str_ends_with($name, '/')) {
                continue;
            }
            if (str_contains($name, '..') || str_starts_with($name, '/') || str_contains($name, ':')) {
                $zip->close();
                throw new InvalidArgumentException('Unsafe path in theme pack ZIP.');
            }
        }

        $dest = rtrim($destDir, DIRECTORY_SEPARATOR);
        if (! is_dir($dest) && ! mkdir($dest, 0775, true) && ! is_dir($dest)) {
            $zip->close();
            throw new InvalidArgumentException('Could not create theme pack directory.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = str_replace('\\', '/', (string) $zip->getNameIndex($i));
            if ($name === '' || str_ends_with($name, '/')) {
                continue;
            }
            $ext = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
            if (! in_array($ext, $allowed, true)) {
                continue;
            }
            $target = $dest.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $name);
            $parent = dirname($target);
            if (! is_dir($parent) && ! mkdir($parent, 0775, true) && ! is_dir($parent)) {
                continue;
            }
            $contents = $zip->getFromIndex($i);
            if ($contents === false) {
                continue;
            }
            file_put_contents($target, $contents);
        }
        $zip->close();

        $manifestPath = $dest.DIRECTORY_SEPARATOR.'theme.json';
        if (! is_file($manifestPath)) {
            throw new InvalidArgumentException('theme.json is required in the theme pack ZIP.');
        }

        return self::parseManifest((string) file_get_contents($manifestPath));
    }

    public static function storagePath(string $id): string
    {
        $id = self::sanitizeId($id);
        $root = function_exists('hub_storage_path')
            ? hub_storage_path('frontend-themes/'.$id)
            : storage_path('app/public/frontend-themes/'.$id);

        return $root;
    }
}
