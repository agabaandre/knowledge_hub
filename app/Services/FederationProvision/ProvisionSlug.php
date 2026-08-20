<?php

namespace App\Services\FederationProvision;

use Illuminate\Support\Str;
use RuntimeException;

class ProvisionSlug
{
    public static function normalize(string $slug): string
    {
        $slug = Str::lower(trim($slug));
        $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug;
    }

    public static function fromCountryName(string $name, ?string $existingSlug = null): string
    {
        if ($existingSlug) {
            $normalized = self::normalize($existingSlug);
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return self::normalize(Str::slug($name));
    }

    public static function assertAllowed(string $slug, ?array $reserved = null): void
    {
        $slug = self::normalize($slug);
        if ($slug === '' || strlen($slug) > 64) {
            throw new RuntimeException('Slug must be 1–64 characters (a-z, 0-9, hyphen).');
        }

        $reserved = array_map('strtolower', $reserved ?? (config('federation_provision.reserved_slugs') ?? []));
        if (in_array($slug, $reserved, true)) {
            throw new RuntimeException('Slug "'.$slug.'" is reserved.');
        }
    }

    public static function databaseName(string $slug): string
    {
        $name = 'khub_'.preg_replace('/[^a-z0-9_]/', '_', self::normalize($slug));

        return substr($name, 0, 64);
    }

    public static function databaseUsername(string $slug): string
    {
        $name = 'khub_'.preg_replace('/[^a-z0-9_]/', '_', self::normalize($slug));

        return substr($name, 0, 32);
    }
}
