<?php

namespace App\Services;

use App\Repositories\PublicationsRepository;
use App\Support\MetricsCache;
use App\Support\SearchCache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeTopSearchesService
{
    public const INITIAL_LIMIT = 6;

    public const LOAD_MORE_LIMIT = 10;

    public const SEARCH_INFINITE_ROWS = 10;

    public function __construct(
        private PublicationsRepository $publicationsRepo
    ) {
    }

    public function publications(Request $request, int $offset, int $limit): Collection
    {
        if ($limit < 1 || $offset < 0) {
            return collect();
        }

        $cacheKey = $this->sliceCacheKey($offset, $limit);
        $ids = MetricsCache::store()->get($cacheKey);
        if (! is_array($ids)) {
            $ids = $this->publicationsRepo->getTopSearchesIds($request, $offset, $limit);
            MetricsCache::store()->put($cacheKey, $ids, MetricsCache::ttl('home_top_searches'));
        }

        if ($ids === []) {
            return collect();
        }

        return $this->publicationsRepo->getTopSearchesByIds($request, $ids);
    }

    public function total(Request $request): int
    {
        $cacheKey = $this->totalCacheKey();
        $cached = MetricsCache::store()->get($cacheKey);
        if (is_int($cached)) {
            return max(0, $cached);
        }

        $total = $this->publicationsRepo->countTopSearches($request);
        MetricsCache::store()->put($cacheKey, $total, MetricsCache::ttl('home_top_searches'));

        return $total;
    }

    private function sliceCacheKey(int $offset, int $limit): string
    {
        return 'home_top_searches_slice:v'.SearchCache::homeTopSearchesVersion().':'.$this->cacheScope().":{$offset}:{$limit}";
    }

    private function totalCacheKey(): string
    {
        return 'home_top_searches_total:v'.SearchCache::homeTopSearchesVersion().':'.$this->cacheScope();
    }

    private function cacheScope(): string
    {
        $userId = auth()->id();

        return $userId ? 'user:'.$userId : 'guest';
    }
}
