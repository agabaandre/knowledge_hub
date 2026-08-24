<?php

namespace App\View\Composers;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TagsViewComposer
{
    public const CACHE_KEY_TAGS_ALPHABETICAL = 'tags_ordered_by_tag_text';
    public const CACHE_KEY_POPULAR_TAGS = 'tags_popular_by_engagement';
    public const POPULAR_TAGS_LIMIT = 40;

    public function compose(View $view)
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $tags = cache()->remember(self::CACHE_KEY_TAGS_ALPHABETICAL, $minutes, function () {
            return Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc')->get();
        });

        $popularTags = cache()->remember(self::CACHE_KEY_POPULAR_TAGS, $minutes, function () {
            return Tag::popularByEngagement(self::POPULAR_TAGS_LIMIT);
        });

        $view->with('tags', $tags);
        $view->with('popular_tags', $popularTags);
    }

    public static function forgetTagListCache(): void
    {
        Cache::forget(self::CACHE_KEY_TAGS_ALPHABETICAL);
        Cache::forget(self::CACHE_KEY_POPULAR_TAGS);
    }
}