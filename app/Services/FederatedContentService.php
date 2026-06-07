<?php

namespace App\Services;

use App\Models\FederatedContentItem;
use App\Models\FederatedKnowledgeHub;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class FederatedContentService
{
    public function federationConsumerEnabled(): bool
    {
        if (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled()) {
            return false;
        }

        return Schema::hasTable('federated_knowledge_hubs');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, FederatedKnowledgeHub>
     */
    public function hubsWithCachedData(?int $hubId = null)
    {
        if (! $this->federationConsumerEnabled()) {
            return collect();
        }

        return FederatedKnowledgeHub::query()
            ->with('mappedCountry')
            ->where('is_active', true)
            ->whereNotNull('cached_public_data')
            ->when($hubId, fn ($q) => $q->whereKey($hubId))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{publications: Collection, forums: Collection}
     */
    public function search(?string $term, int $limit = 20, ?int $hubId = null): array
    {
        $term = mb_strtolower(trim((string) $term));

        $publications = collect();
        $forums = collect();

        foreach ($this->hubsWithCachedData($hubId) as $hub) {
            foreach ($this->publicationItemsFromHub($hub) as $item) {
                if ($term === '' || $this->matchesTerm($item, $term, ['title', 'description'])) {
                    $publications->push($item);
                }
            }
            foreach ($this->forumItemsFromHub($hub) as $item) {
                if ($term === '' || $this->matchesTerm($item, $term, ['forum_title', 'forum_description', 'title', 'description'])) {
                    $forums->push($item);
                }
            }
        }

        return [
            'publications' => $publications->take($limit)->values(),
            'forums' => $forums->take($limit)->values(),
        ];
    }

    /**
     * Paginated browse listings from synced cache.
     *
     * @return array{
     *     hubs: Collection,
     *     publications: LengthAwarePaginator,
     *     forums: LengthAwarePaginator,
     *     activeHub: ?FederatedKnowledgeHub,
     *     type: string
     * }
     */
    public function browse(Request $request): array
    {
        $hubId = $request->filled('hub') ? (int) $request->input('hub') : null;
        $type = $request->input('type', 'publications');
        $perPage = min(50, max(10, (int) $request->input('per_page', 20)));
        $page = max(1, (int) $request->input('page', 1));
        $term = trim((string) $request->input('term', ''));

        $hubs = $this->hubsWithCachedData();
        $activeHub = $hubId ? $hubs->firstWhere('id', $hubId) : null;

        $publications = collect();
        $forums = collect();

        foreach ($this->hubsWithCachedData($hubId) as $hub) {
            $publications = $publications->merge($this->publicationItemsFromHub($hub));
            $forums = $forums->merge($this->forumItemsFromHub($hub));
        }

        if ($term !== '') {
            $needle = mb_strtolower($term);
            $publications = $publications->filter(fn ($item) => $this->matchesTerm($item, $needle, ['title', 'description']))->values();
            $forums = $forums->filter(fn ($item) => $this->matchesTerm($item, $needle, ['forum_title', 'forum_description', 'title', 'description']))->values();
        }

        $publications = $publications->sortByDesc(fn ($item) => $item->sort_at ?? '')->values();
        $forums = $forums->sortByDesc(fn ($item) => $item->sort_at ?? '')->values();

        return [
            'hubs' => $hubs,
            'activeHub' => $activeHub,
            'type' => in_array($type, ['publications', 'forums'], true) ? $type : 'publications',
            'term' => $term,
            'publications' => $this->paginateCollection($publications, $perPage, $page, $request, 'page', ['type' => 'publications']),
            'forums' => $this->paginateCollection($forums, $perPage, $page, $request, 'page', ['type' => 'forums']),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    protected function publicationItemsFromHub(FederatedKnowledgeHub $hub): Collection
    {
        if (Schema::hasTable('federated_content_items')) {
            return FederatedContentItem::query()
                ->approved()
                ->where('federated_knowledge_hub_id', $hub->id)
                ->where('content_type', 'publication')
                ->orderByDesc('remote_updated_at')
                ->get()
                ->map(fn (FederatedContentItem $item) => $this->normalizePublication((array) $item->payload, $hub));
        }

        $items = data_get($hub->cached_public_data, 'publications.data', []);

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->map(fn ($item) => $this->normalizePublication((array) $item, $hub));
    }

    /**
     * @return Collection<int, object>
     */
    protected function forumItemsFromHub(FederatedKnowledgeHub $hub): Collection
    {
        if (Schema::hasTable('federated_content_items')) {
            return FederatedContentItem::query()
                ->approved()
                ->where('federated_knowledge_hub_id', $hub->id)
                ->where('content_type', 'forum')
                ->orderByDesc('remote_updated_at')
                ->get()
                ->map(fn (FederatedContentItem $item) => $this->normalizeForum((array) $item->payload, $hub));
        }

        $items = data_get($hub->cached_public_data, 'forums.data', []);

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->map(fn ($item) => $this->normalizeForum((array) $item, $hub));
    }

    protected function normalizePublication(array $item, FederatedKnowledgeHub $hub): object
    {
        $base = $hub->normalizedBaseUrl();
        $id = (int) ($item['id'] ?? 0);
        $slug = $item['slug'] ?? null;
        $title = $item['title'] ?? 'Untitled resource';
        $description = $item['description'] ?? '';

        return (object) [
            'is_federated' => true,
            'id' => $id,
            'slug' => $slug,
            'title' => $title,
            'description' => $description,
            'cover' => $item['cover'] ?? null,
            'image_url' => $this->resolveRemoteCoverUrl($base, $item['cover'] ?? null),
            'year_published' => $item['year_published'] ?? null,
            'created_at' => $item['created_at'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
            'sort_at' => $item['updated_at'] ?? $item['created_at'] ?? null,
            'federation_hub_id' => $hub->id,
            'federation_hub_name' => $hub->name,
            'federation_hub_url' => $base,
            'federation_source_url' => federated_publication_url($base, $item),
            'federation_country' => optional($hub->mappedCountry)->name,
        ];
    }

    protected function normalizeForum(array $item, FederatedKnowledgeHub $hub): object
    {
        $base = $hub->normalizedBaseUrl();
        $title = $item['forum_title'] ?? $item['title'] ?? 'Untitled discussion';
        $description = $item['forum_description'] ?? $item['description'] ?? '';

        return (object) [
            'is_federated' => true,
            'id' => (int) ($item['id'] ?? 0),
            'slug' => $item['slug'] ?? null,
            'forum_title' => $title,
            'forum_description' => $description,
            'forum_image' => $item['forum_image'] ?? null,
            'created_at' => $item['created_at'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
            'sort_at' => $item['updated_at'] ?? $item['created_at'] ?? null,
            'total_comments' => (int) ($item['total_comments'] ?? 0),
            'views' => (int) ($item['views'] ?? 0),
            'federation_hub_id' => $hub->id,
            'federation_hub_name' => $hub->name,
            'federation_hub_url' => $base,
            'federation_source_url' => federated_forum_url($base, $item),
            'federation_country' => optional($hub->mappedCountry)->name,
        ];
    }

    protected function resolveRemoteCoverUrl(string $hubBase, ?string $cover): ?string
    {
        if ($cover === null || $cover === '') {
            return null;
        }

        if (filter_var($cover, FILTER_VALIDATE_URL)) {
            return $cover;
        }

        return rtrim($hubBase, '/').'/hub-media/uploads/publications/'.ltrim($cover, '/');
    }

    /**
     * @param  array<int, string>  $fields
     */
    protected function matchesTerm(object $item, string $needle, array $fields): bool
    {
        foreach ($fields as $field) {
            $value = mb_strtolower(strip_tags((string) ($item->{$field} ?? '')));
            if ($value !== '' && str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function paginateCollection(
        Collection $items,
        int $perPage,
        int $page,
        Request $request,
        string $pageName = 'page',
        array $query = []
    ): LengthAwarePaginator {
        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
                'query' => array_merge($request->query(), $query),
            ]
        );
    }
}
