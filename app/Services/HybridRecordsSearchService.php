<?php

namespace App\Services;

use App\Models\Publication;
use App\Repositories\PublicationsRepository;
use App\Support\MetricsCache;
use App\Support\SearchCache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HybridRecordsSearchService
{
    private const ID_POOL_LIMIT = 300;

    private const MEILI_ID_LIMIT = 120;

    public function __construct(
        private PublicationsRepository $publicationsRepo
    ) {
    }

    /**
     * Apply Meilisearch relevance ranking + Redis-cached ID pools to a publication listing request.
     */
    public function preparePublicationSearchRequest(Request $request): Request
    {
        $prepared = clone $request;
        $prepared->merge(['search_listing' => true]);

        $term = trim((string) ($request->term ?? ''));
        if ($term === '' || mb_strlen($term) < 2) {
            return $prepared;
        }

        $prepared->merge(['skip_random_order' => true]);

        if (! $this->meilisearchAvailable()) {
            return $prepared;
        }

        $page = max(1, (int) $request->input('page', 1));
        $perPage = max(1, min(100, (int) ($request->rows ?? 20)));
        $idPool = $this->resolvePublicationIdPool($request);

        if ($idPool === []) {
            return $prepared;
        }

        $offset = ($page - 1) * $perPage;
        $pageIds = array_slice($idPool, $offset, $perPage);

        if ($pageIds === []) {
            $prepared->merge([
                'restrict_publication_ids' => [0],
                'skip_sql_search_term' => true,
                'search_total_override' => count($idPool),
            ]);

            return $prepared;
        }

        $prepared->merge([
            'restrict_publication_ids' => $pageIds,
            'skip_sql_search_term' => true,
            'search_total_override' => count($idPool),
        ]);

        return $prepared;
    }

    /**
     * Publications for AI insights: Meilisearch relevance + SQL recall, cached in Redis when available.
     */
    public function publicationsForAi(Request $request, int $limit = 15): Collection
    {
        $term = trim((string) ($request->term ?? ''));
        if ($term === '') {
            return collect();
        }

        $ids = $this->resolvePublicationIdPool($request, max($limit * 3, 30));
        $aiRequest = clone $request;
        $aiRequest->merge([
            'rows' => $limit,
            'page' => 1,
            'skip_random_order' => true,
            'search_listing' => true,
        ]);

        if ($ids !== []) {
            $aiRequest->merge([
                'restrict_publication_ids' => array_slice($ids, 0, max($limit * 2, 20)),
                'skip_sql_search_term' => true,
            ]);
        }

        $rows = $this->publicationsRepo->get($aiRequest);

        return method_exists($rows, 'getCollection') ? $rows->getCollection() : collect($rows);
    }

    /**
     * @return list<int>
     */
    public function resolvePublicationIds(Request $request, int $limit = 45): array
    {
        return array_slice($this->resolvePublicationIdPool($request, $limit), 0, $limit);
    }

    /**
     * @return list<int>
     */
    public function resolvePublicationIdPool(Request $request, int $limit = self::ID_POOL_LIMIT): array
    {
        $term = trim((string) ($request->term ?? ''));
        if ($term === '' || $limit < 1) {
            return [];
        }

        $cacheKey = $this->idPoolCacheKey($request);
        $cached = MetricsCache::store()->get($cacheKey);
        if (is_array($cached)) {
            return array_slice(array_values(array_map('intval', $cached)), 0, $limit);
        }

        $meiliIds = $this->meilisearchPublicationIds($term, $request, min($limit, self::MEILI_ID_LIMIT));
        $sqlIds = $this->sqlPublicationIds($request, min($limit, 80));
        $merged = array_values(array_unique(array_merge($meiliIds, $sqlIds)));

        MetricsCache::store()->put($cacheKey, $merged, MetricsCache::ttl('search_results'));

        return array_slice($merged, 0, $limit);
    }

    /**
     * @return list<int>
     */
    private function meilisearchPublicationIds(string $term, Request $request, int $limit): array
    {
        if (! $this->meilisearchAvailable()) {
            return [];
        }

        try {
            $builder = Publication::search($term);

            $subThemeId = (int) ($request->input('sub_thematic_area_id') ?: $request->input('subtheme') ?: 0);
            if ($subThemeId > 0) {
                $builder->where('sub_thematic_area_id', $subThemeId);
            }

            $themeId = (int) ($request->input('thematic_area_id') ?: $request->input('theme') ?: 0);
            if ($themeId > 0) {
                $builder->where('thematic_area_id', $themeId);
            }

            $authorId = (int) ($request->input('author_id') ?: $request->input('author') ?: 0);
            if ($authorId > 0) {
                $builder->where('author_id', $authorId);
            }

            $fileTypeId = (int) ($request->input('file_type_id') ?: $request->input('file_type') ?: 0);
            if ($fileTypeId > 0) {
                $builder->where('file_type_id', $fileTypeId);
            }

            foreach (['data_category_id', 'file_category_id'] as $param) {
                $ids = $this->normalizeFilterIds($request, $param);
                if ($ids !== null && count($ids) === 1) {
                    $field = $param === 'data_category_id' ? 'publication_catgory_id' : 'data_category_id';
                    $builder->where($field, $ids[0]);
                }
            }

            $tagId = (int) ($request->input('tag') ?: 0);
            if ($tagId > 0) {
                $builder->where('tag_ids', $tagId);
            }

            return $builder
                ->take(min($limit, 100))
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::debug('hybrid_records_search.meilisearch_failed', [
                'term' => $term,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return list<int>
     */
    private function sqlPublicationIds(Request $request, int $limit): array
    {
        try {
            $sqlRequest = clone $request;
            $sqlRequest->merge([
                'rows' => min($limit, 100),
                'page' => 1,
                'skip_random_order' => true,
                'search_listing' => true,
                'select_ids_only' => true,
            ]);

            $ids = $this->publicationsRepo->getPublicationIds($sqlRequest, $limit);

            return array_values(array_filter(array_map('intval', $ids), fn (int $id) => $id > 0));
        } catch (\Throwable $e) {
            Log::debug('hybrid_records_search.sql_failed', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function meilisearchAvailable(): bool
    {
        if ((string) config('scout.driver', 'collection') !== 'meilisearch') {
            return false;
        }

        $cacheStore = MetricsCache::store();
        $cached = $cacheStore->get('meilisearch_health_ok');
        if ($cached !== null) {
            return (bool) $cached;
        }

        $host = rtrim(trim((string) config('scout.meilisearch.host', env('MEILISEARCH_HOST', ''))), '/');
        if ($host === '') {
            $cacheStore->put('meilisearch_health_ok', false, 30);

            return false;
        }

        try {
            $response = Http::timeout(2)->get($host.'/health');
            $ok = $response->successful();
            $cacheStore->put('meilisearch_health_ok', $ok, 30);

            return $ok;
        } catch (\Throwable $e) {
            $cacheStore->put('meilisearch_health_ok', false, 30);

            return false;
        }
    }

    private function idPoolCacheKey(Request $request): string
    {
        return 'records_search_ids:v'.SearchCache::hybridVersion().':'.md5(
            mb_strtolower(trim((string) ($request->term ?? ''))).'|'.$this->filterFingerprint($request)
        );
    }

    private function filterFingerprint(Request $request): string
    {
        $keys = [
            'thematic_area_id', 'theme', 'sub_thematic_area_id', 'subtheme', 'country_id', 'data_category_id',
            'file_category_id', 'author_id', 'author', 'file_type_id', 'file_type', 'rcc', 'tag',
        ];
        $parts = [];
        foreach ($keys as $key) {
            $parts[] = $key.'='.json_encode($request->input($key));
        }

        return implode(';', $parts);
    }

    /**
     * @return list<int>|null
     */
    private function normalizeFilterIds(Request $request, string $key): ?array
    {
        $raw = $request->input($key);
        if ($raw === null || $raw === '' || $raw === 'all') {
            return null;
        }
        if (! is_array($raw)) {
            $raw = [$raw];
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $raw), fn (int $id) => $id > 0)));

        return $ids === [] ? null : $ids;
    }
}
