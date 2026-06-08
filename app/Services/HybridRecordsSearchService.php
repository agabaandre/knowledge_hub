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
    public function __construct(
        private PublicationsRepository $publicationsRepo
    ) {
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

        $ids = $this->resolvePublicationIds($request, max($limit * 3, 30));
        $aiRequest = clone $request;
        $aiRequest->merge([
            'rows' => $limit,
            'page' => 1,
            'skip_random_order' => true,
        ]);

        if ($ids !== []) {
            $aiRequest->merge(['restrict_publication_ids' => array_slice($ids, 0, max($limit * 2, 20))]);
        }

        $rows = $this->publicationsRepo->get($aiRequest);

        return method_exists($rows, 'getCollection') ? $rows->getCollection() : collect($rows);
    }

    /**
     * @return list<int>
     */
    public function resolvePublicationIds(Request $request, int $limit = 45): array
    {
        $term = trim((string) ($request->term ?? ''));
        if ($term === '' || $limit < 1) {
            return [];
        }

        $cacheKey = 'hybrid_records_search:v'.SearchCache::hybridVersion().':'.md5($term.'|'.$this->filterFingerprint($request));
        $cached = MetricsCache::store()->get($cacheKey);
        if (is_array($cached)) {
            return array_slice(array_values(array_map('intval', $cached)), 0, $limit);
        }

        $meiliIds = $this->meilisearchPublicationIds($term, $limit);
        $sqlIds = $this->sqlPublicationIds($request, $limit);
        $merged = array_values(array_unique(array_merge($meiliIds, $sqlIds)));

        MetricsCache::store()->put($cacheKey, $merged, MetricsCache::ttl('filtered'));

        return array_slice($merged, 0, $limit);
    }

    /**
     * @return list<int>
     */
    private function meilisearchPublicationIds(string $term, int $limit): array
    {
        if (! $this->meilisearchAvailable()) {
            return [];
        }

        try {
            return Publication::search($term)
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
            ]);
            $rows = $this->publicationsRepo->get($sqlRequest);
            $collection = method_exists($rows, 'getCollection') ? $rows->getCollection() : collect($rows);

            return $collection
                ->map(fn ($pub) => (int) ($pub->id ?? 0))
                ->filter(fn (int $id) => $id > 0)
                ->values()
                ->all();
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

        $host = rtrim(trim((string) config('scout.meilisearch.host', env('MEILISEARCH_HOST', ''))), '/');
        if ($host === '') {
            return false;
        }

        try {
            $response = Http::timeout(2)->get($host.'/health');

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function filterFingerprint(Request $request): string
    {
        $keys = [
            'thematic_area_id', 'sub_thematic_area_id', 'country_id', 'data_category_id',
            'author_id', 'file_type_id', 'rcc', 'tag',
        ];
        $parts = [];
        foreach ($keys as $key) {
            $parts[] = $key.'='.json_encode($request->input($key));
        }

        return implode(';', $parts);
    }
}
