<?php

use App\Models\Author;
use App\Models\CommunityOfPractice;
use App\Models\Country;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\Tag;

if (! function_exists('seo_friendly_urls_enabled')) {
    function seo_friendly_urls_enabled(): bool
    {
        if (! function_exists('settings')) {
            return false;
        }

        $value = settings()->use_seo_friendly_urls ?? null;

        return $value === null ? true : (bool) $value;
    }
}

if (! function_exists('resolve_tag_for_url')) {
    /**
     * @param  Tag|object|int|string|null  $tag
     * @return array{id: ?int, slug: ?string}
     */
    function resolve_tag_for_url($tag): array
    {
        $id = null;
        $slug = null;

        if ($tag instanceof Tag) {
            $id = (int) $tag->id;
            $slug = $tag->slug ?? null;
        } elseif (is_object($tag) && isset($tag->id)) {
            $id = (int) $tag->id;
            $slug = $tag->slug ?? null;
        } elseif (is_numeric($tag)) {
            $id = (int) $tag;
            $slug = Tag::query()->whereKey($id)->value('slug');
        }

        return ['id' => $id, 'slug' => $slug];
    }
}

if (! function_exists('resolve_community_for_url')) {
    /**
     * @param  CommunityOfPractice|object|int|string|null  $community
     * @return array{id: ?int, slug: ?string}
     */
    function resolve_community_for_url($community): array
    {
        $id = null;
        $slug = null;

        if ($community instanceof CommunityOfPractice) {
            $id = (int) $community->id;
            $slug = $community->slug ?? null;
        } elseif (is_object($community) && isset($community->id)) {
            $id = (int) $community->id;
            $slug = $community->slug ?? null;
        } elseif (is_numeric($community)) {
            $id = (int) $community;
            $slug = CommunityOfPractice::query()->whereKey($id)->value('slug');
        }

        return ['id' => $id, 'slug' => $slug];
    }
}

if (! function_exists('publication_url')) {
    /**
     * @param  Publication|object|int|string|null  $publication
     */
    function publication_url($publication, bool $absolute = true, array $query = []): string
    {
        $id = null;
        $slug = null;

        if ($publication instanceof Publication) {
            $id = (int) $publication->id;
            $slug = $publication->slug ?? null;
        } elseif (is_object($publication) && isset($publication->id)) {
            $id = (int) $publication->id;
            $slug = $publication->slug ?? null;
        } elseif (is_numeric($publication)) {
            $id = (int) $publication;
            $slug = Publication::query()->whereKey($id)->value('slug');
        }

        if (! $id) {
            $path = 'records/resource';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'records/resource/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'records/resource?id='.$id;
            if ($query) {
                $path .= '&'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('forum_thread_url')) {
    /**
     * @param  Forum|object|int|string|null  $forum
     */
    function forum_thread_url($forum, bool $absolute = true, array $query = []): string
    {
        $id = null;
        $slug = null;

        if ($forum instanceof Forum) {
            $id = (int) $forum->id;
            $slug = $forum->slug ?? null;
        } elseif (is_object($forum) && isset($forum->id)) {
            $id = (int) $forum->id;
            $slug = $forum->slug ?? null;
        } elseif (is_numeric($forum)) {
            $id = (int) $forum;
            $slug = Forum::query()->whereKey($id)->value('slug');
        }

        if (! $id) {
            $path = 'forums/thread';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'forums/thread/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'forums/thread?id='.$id;
            if ($query) {
                $path .= '&'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('tag_records_url')) {
    /**
     * @param  Tag|object|int|string|null  $tag
     */
    function tag_records_url($tag, bool $absolute = true, array $query = []): string
    {
        ['id' => $id, 'slug' => $slug] = resolve_tag_for_url($tag);

        if (! $id) {
            $path = 'records';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'records/tag/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'records?tag='.$id;
            if ($query) {
                $path .= '&'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('health_topic_url')) {
    /**
     * @param  Tag|object|int|string|null  $tag
     */
    function health_topic_url($tag, bool $absolute = true, array $query = []): string
    {
        ['id' => $id, 'slug' => $slug] = resolve_tag_for_url($tag);

        if (! $id) {
            $path = 'health-topics';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'health-topics/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'health-topics/'.$id;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('community_detail_url')) {
    /**
     * @param  CommunityOfPractice|object|int|string|null  $community
     */
    function community_detail_url($community, bool $absolute = true, array $query = []): string
    {
        ['id' => $id, 'slug' => $slug] = resolve_community_for_url($community);

        if (! $id) {
            $path = 'communities';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'communities/detail/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'communities/detail/'.$id;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('resolve_country_for_url')) {
    /**
     * @param  Country|object|int|string|null  $country
     * @return array{id: ?int, slug: ?string}
     */
    function resolve_country_for_url($country): array
    {
        $id = null;
        $slug = null;

        if ($country instanceof Country) {
            $id = (int) $country->id;
            $slug = $country->slug ?? null;
        } elseif (is_object($country) && isset($country->id)) {
            $id = (int) $country->id;
            $slug = $country->slug ?? null;
        } elseif (is_numeric($country)) {
            $id = (int) $country;
            $slug = Country::query()->whereKey($id)->value('slug');
        }

        return ['id' => $id, 'slug' => $slug];
    }
}

if (! function_exists('resolve_author_for_url')) {
    /**
     * @param  Author|object|int|string|null  $author
     * @return array{id: ?int, slug: ?string}
     */
    function resolve_author_for_url($author): array
    {
        $id = null;
        $slug = null;

        if ($author instanceof Author) {
            $id = (int) $author->id;
            $slug = $author->slug ?? null;
        } elseif (is_object($author) && isset($author->id)) {
            $id = (int) $author->id;
            $slug = $author->slug ?? null;
        } elseif (is_numeric($author)) {
            $id = (int) $author;
            $slug = Author::query()->whereKey($id)->value('slug');
        }

        return ['id' => $id, 'slug' => $slug];
    }
}

if (! function_exists('user_author_publications_url')) {
    /**
     * Profile URL for a hub user when they are linked to an author record.
     */
    function user_author_publications_url(?\App\Models\User $user, bool $absolute = true): ?string
    {
        if ($user === null) {
            return null;
        }

        if (! empty($user->author_id)) {
            return author_publications_url((int) $user->author_id, $absolute);
        }

        if ($user->relationLoaded('author') && $user->author) {
            return author_publications_url($user->author, $absolute);
        }

        return null;
    }
}

if (! function_exists('author_publications_url')) {
    /**
     * @param  Author|object|int|string|null  $author
     */
    function author_publications_url($author, bool $absolute = true, array $query = []): string
    {
        ['id' => $id, 'slug' => $slug] = resolve_author_for_url($author);

        if (! $id) {
            $path = 'authors/publications';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'authors/publications/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'authors/publications?author='.$id;
            if ($query) {
                $path .= '&'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}

if (! function_exists('country_detail_url')) {
    /**
     * Member state detail page (countries/details).
     *
     * @param  Country|object|int|string|null  $country
     */
    function country_detail_url($country, bool $absolute = true, array $query = []): string
    {
        ['id' => $id, 'slug' => $slug] = resolve_country_for_url($country);

        if (! $id) {
            $path = 'countries';
            if ($query) {
                $path .= '?'.http_build_query($query);
            }

            return $absolute ? url($path) : $path;
        }

        if (seo_friendly_urls_enabled() && ! empty($slug)) {
            $path = 'countries/details/'.$slug;
            if ($query) {
                $path .= '?'.http_build_query($query);
            }
        } else {
            $path = 'countries/details?state='.$id;
            if ($query) {
                $path .= '&'.http_build_query($query);
            }
        }

        return $absolute ? url($path) : $path;
    }
}
