<?php

namespace App\Support;

use Illuminate\Http\Request;

final class RecordsSearchFragmentCache
{
    public static function get(Request $request): ?array
    {
        $cached = MetricsCache::store()->get(self::key($request));

        return is_array($cached) ? $cached : null;
    }

    public static function put(Request $request, array $payload): void
    {
        MetricsCache::store()->put(
            self::key($request),
            $payload,
            MetricsCache::ttl('search_results')
        );
    }

    private static function key(Request $request): string
    {
        $term = PublicationSearchQuery::normalizeTerm((string) ($request->term ?? ''));

        return 'records_search_fragment:v'.SearchCache::hybridVersion().':'.md5(
            mb_strtolower($term).'|'.json_encode(self::filterFingerprint($request))
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function filterFingerprint(Request $request): array
    {
        $keys = [
            'thematic_area_id', 'theme', 'sub_thematic_area_id', 'subtheme', 'country_id',
            'data_category_id', 'file_category_id', 'author_id', 'author', 'file_type_id',
            'file_type', 'rcc', 'tag', 'page', 'rows',
        ];
        $parts = [];
        foreach ($keys as $key) {
            $parts[$key] = $request->input($key);
        }

        return $parts;
    }
}
