<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeoSlugger
{
    public static function forPublication(string $title, ?int $excludeId = null): string
    {
        return self::uniqueSlug('publication', self::baseSlug($title, 'publication'), $excludeId);
    }

    public static function forForum(string $title, ?int $excludeId = null): string
    {
        return self::uniqueSlug('forums', self::baseSlug($title, 'forum'), $excludeId);
    }

    public static function forTag(string $title, ?int $excludeId = null): string
    {
        return self::uniqueSlug('tags', self::baseSlug($title, 'tag'), $excludeId);
    }

    public static function forCommunity(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('community_of_practices', self::baseSlug($name, 'community'), $excludeId);
    }

    public static function forCountry(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('country', self::baseSlug($name, 'country'), $excludeId);
    }

    public static function forAuthor(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('author', self::baseSlug($name, 'contributor'), $excludeId);
    }

    public static function forThematicArea(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('thematic_area', self::baseSlug($name, 'theme'), $excludeId);
    }

    public static function forSubThematicArea(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('sub_thematic_area', self::baseSlug($name, 'sub-theme'), $excludeId);
    }

    public static function forDataCategory(string $name, ?int $excludeId = null): string
    {
        $table = (new \App\Models\DataCategory())->getTable();

        return self::uniqueSlug($table, self::baseSlug($name, 'category'), $excludeId);
    }

    public static function forSubjectArea(string $name, ?int $excludeId = null): string
    {
        return self::uniqueSlug('subject_areas', self::baseSlug($name, 'subject-area'), $excludeId);
    }

    public static function baseSlug(string $title, string $fallback): string
    {
        $slug = Str::slug(Str::limit(trim($title), 120, ''));
        if ($slug === '') {
            $slug = $fallback;
        }

        return Str::limit($slug, 180, '');
    }

    protected static function uniqueSlug(string $table, string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $suffix = 0;

        while (self::slugExists($table, $slug, $excludeId)) {
            $suffix++;
            $slug = Str::limit($base, 170, '').'-'.$suffix;
        }

        return $slug;
    }

    protected static function slugExists(string $table, string $slug, ?int $excludeId = null): bool
    {
        $query = DB::table($table)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
