<?php

namespace App\View\Composers;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TagsViewComposer
{
    public const CACHE_KEY_TAGS_ALPHABETICAL = 'tags_ordered_by_tag_text';

    public function compose(View $view)
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $tags = cache()->remember(self::CACHE_KEY_TAGS_ALPHABETICAL, $minutes, function () {
            return Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc')->get();
        });

        $view->with('tags', $tags);
    }

    public static function forgetTagListCache(): void
    {
        Cache::forget(self::CACHE_KEY_TAGS_ALPHABETICAL);
    }
}