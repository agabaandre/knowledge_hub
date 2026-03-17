<?php

namespace App\View\Composers;

use App\Models\Tag;
use Illuminate\View\View;

class TagsViewComposer
{
    public function compose(View $view)
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        // Popular tags = tags with approved publications, ordered by engagement (views + likes)
        $tags = cache()->remember('popular_tags_by_engagement', $minutes, function () {
            return Tag::popularByEngagement(20);
        });

        $view->with('tags', $tags);
    }
}