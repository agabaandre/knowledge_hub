<?php

namespace App\Services;

use App\Models\Country;
use App\Models\FederatedKnowledgeHub;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\Region;
use App\Support\HubSiteIdentifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class FederatedHubService
{
    public function localManifest(): array
    {
        $settings = function_exists('settings') ? settings() : null;
        $countryId = hub_owner_country_id();
        $regionId = hub_owner_region_id();
        $country = $countryId ? Country::find($countryId) : null;
        $region = $regionId ? Region::find($regionId) : null;

        return [
            'api_version' => '1.0',
            'site_id' => env('HUB_SITE_ID') ?: HubSiteIdentifier::fromAppUrl(),
            'app_url' => rtrim((string) config('app.url'), '/'),
            'hub_type' => hub_admin_units_enabled() ? 'country' : 'continental',
            'site_name' => $settings->site_name ?? config('app.name'),
            'title' => $settings->title ?? null,
            'site_description' => $settings->site_description ?? null,
            'slogan' => $settings->slogan ?? null,
            'timezone' => $settings->timezone ?? config('app.timezone'),
            'admin_units_enabled' => hub_admin_units_enabled(),
            'states_enabled' => function_exists('states_enabled') ? states_enabled() : (bool) config('deployment.states_enabled'),
            'owner_country' => $country ? [
                'id' => (int) $country->id,
                'name' => $country->name ?? null,
                'code' => $country->code ?? $country->country_code ?? null,
            ] : null,
            'owner_region' => $region ? [
                'id' => (int) $region->id,
                'name' => $region->region_name ?? $region->name ?? null,
            ] : null,
            'generated_at' => now()->toIso8601String(),
            'lookup' => app(FederatedHubLookupService::class)->lookupIndex(),
        ];
    }

    public function publicPublicationsPayload(int $perPage = 20, int $page = 1): array
    {
        if (! Schema::hasColumn('publication', 'public_availability')) {
            return ['data' => [], 'meta' => ['total' => 0, 'page' => $page, 'per_page' => $perPage]];
        }

        $query = Publication::query()
            ->where('public_availability', 1)
            ->where('is_approved', 1)
            ->where('is_active', 'Active')
            ->orderByDesc('id');

        $paginator = $query->paginate($perPage, [
            'id', 'title', 'description', 'slug', 'year_published', 'created_at', 'updated_at',
            'geographical_coverage_id', 'publication_catgory_id', 'sub_thematic_area_id', 'cover',
        ], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }

    public function publicForumsPayload(int $perPage = 20, int $page = 1): array
    {
        if (! Schema::hasColumn('forums', 'public_availability')) {
            return ['data' => [], 'meta' => ['total' => 0, 'page' => $page, 'per_page' => $perPage]];
        }

        $query = Forum::query()
            ->where('public_availability', 1)
            ->where('is_approved', 1)
            ->where('status', 1)
            ->orderByDesc('id');

        $paginator = $query->paginate($perPage, [
            'id', 'forum_title', 'forum_description', 'slug', 'forum_image', 'created_at', 'updated_at', 'created_by',
        ], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }

    public function federationToken(): ?string
    {
        $settings = function_exists('settings') ? settings() : null;
        if ($settings && Schema::hasColumn('setting', 'federation_api_token') && $settings->federation_api_token) {
            return (string) $settings->federation_api_token;
        }

        $envToken = env('FEDERATION_API_TOKEN');

        return $envToken !== null && $envToken !== '' ? (string) $envToken : null;
    }

    public function testConnection(FederatedKnowledgeHub $hub): array
    {
        try {
            if ($hub->api_token && ! $hub->api_refresh_token) {
                try {
                    app(FederationHubAuthService::class)->bootstrapRemoteHubTokens($hub, $hub->api_token);
                } catch (\Throwable) {
                    // Parent hub may still use a static bearer token only.
                }
            }

            $manifest = $this->fetchRemoteManifest($hub);
            $hub->connection_status = 'connected';
            $hub->connection_error = null;
            $hub->remote_site_id = $manifest['site_id'] ?? $hub->remote_site_id;
            $hub->last_manifest = $manifest;
            $hub->save();

            return ['ok' => true, 'manifest' => $manifest];
        } catch (\Throwable $e) {
            $hub->connection_status = 'failed';
            $hub->connection_error = $e->getMessage();
            $hub->save();

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncPublicData(FederatedKnowledgeHub $hub, int $perPage = 100): array
    {
        $publications = $this->fetchAllPaginated($hub, '/api/federation/public/publications', $perPage);
        $forums = $this->fetchAllPaginated($hub, '/api/federation/public/forums', $perPage);

        $hub->cached_public_data = [
            'publications' => $publications,
            'forums' => $forums,
            'synced_at' => now()->toIso8601String(),
        ];
        $hub->last_synced_at = now();
        $hub->connection_status = 'connected';
        $hub->connection_error = null;
        $hub->save();

        app(FederatedContentStagingService::class)->stageFromSync($hub, $hub->cached_public_data);

        return $hub->cached_public_data;
    }

    /**
     * @return array{data: array<int, mixed>, meta: array<string, mixed>}
     */
    public function fetchAllPaginated(FederatedKnowledgeHub $hub, string $path, int $perPage = 100): array
    {
        $perPage = min(100, max(1, $perPage));
        $allData = [];
        $page = 1;
        $lastPage = 1;
        $maxPages = 500;

        do {
            $response = $this->remoteGet($hub, $path.'?page='.$page.'&per_page='.$perPage);
            $items = $response['data'] ?? [];
            if (is_array($items)) {
                $allData = array_merge($allData, $items);
            }
            $lastPage = max(1, (int) data_get($response, 'meta.last_page', 1));
            $page++;
        } while ($page <= $lastPage && $page <= $maxPages);

        return [
            'data' => $allData,
            'meta' => [
                'total' => count($allData),
                'pages_fetched' => min($page - 1, $lastPage),
                'per_page' => $perPage,
            ],
        ];
    }

    public function fetchRemoteManifest(FederatedKnowledgeHub $hub): array
    {
        $response = $this->remoteGet($hub, '/api/federation/manifest');

        if (! ($response['manifest'] ?? null)) {
            throw new \RuntimeException('Remote hub did not return a manifest.');
        }

        return $response['manifest'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetchRemoteJson(FederatedKnowledgeHub $hub, string $path): array
    {
        return $this->remoteGet($hub, $path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function remoteGet(FederatedKnowledgeHub $hub, string $path): array
    {
        $url = $hub->normalizedBaseUrl().$path;
        $auth = app(FederationHubAuthService::class);
        $registrationToken = $hub->api_token && ! $hub->api_refresh_token ? $hub->api_token : null;

        try {
            return $auth->remoteHubAuthorizedRequest(
                $hub,
                fn ($request) => $request->get($url),
                $registrationToken
            );
        } catch (\Throwable $e) {
            if (! $hub->api_token) {
                throw $e;
            }

            $response = Http::timeout(20)->acceptJson()->withToken($hub->api_token)->get($url);
            if (! $response->successful()) {
                throw new \RuntimeException('Remote hub request failed ('.$response->status().'): '.$response->body());
            }

            $json = $response->json();

            return is_array($json) ? $json : [];
        }
    }
}
