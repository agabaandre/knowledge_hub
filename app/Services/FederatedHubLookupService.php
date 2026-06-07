<?php

namespace App\Services;

use App\Models\DataCategory;
use App\Models\License;
use App\Models\PublicationCategory;
use App\Models\StaticLink;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\ThemeticArea;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class FederatedHubLookupService
{
    public function lookupIndex(): array
    {
        $base = rtrim((string) config('app.url'), '/').'/api/federation';

        return [
            'api_version' => '1.0',
            'endpoints' => [
                'auth_token' => $base.'/auth/token',
                'manifest' => $base.'/manifest',
                'public_publications' => $base.'/public/publications',
                'public_forums' => $base.'/public/forums',
                'lookup_settings' => $base.'/lookup/settings',
                'lookup_metadata' => $base.'/lookup/metadata',
                'lookup_index' => $base.'/lookup',
            ],
            'description' => 'Federated Knowledge Hub lookup and content exchange API.',
        ];
    }

    /**
     * Branding + mobile settings payload (mirrors /api/lookup/settings subset).
     *
     * @return array<string, mixed>
     */
    public function exportBrandingPayload(): array
    {
        $settings = function_exists('settings') ? settings() : null;
        if (! $settings) {
            return ['settings' => [], 'theme_settings' => []];
        }

        $keys = config('federation.branding_setting_keys', []);
        $imageKeys = config('federation.branding_image_keys', []);
        $exported = [];

        foreach ($keys as $key) {
            if (isset($settings->{$key})) {
                $exported[$key] = $settings->{$key};
            }
        }

        foreach ($imageKeys as $key) {
            $value = $settings->{$key} ?? null;
            if ($value !== null && $value !== '') {
                $exported[$key] = $value;
            }
        }

        $themeKey = $this->activeThemeKey($settings);
        $themeSettings = [];
        if ($themeKey && Schema::hasTable('theme_settings')) {
            $themeSettings = DB::table('theme_settings')
                ->where('theme', $themeKey)
                ->pluck('value', 'key')
                ->toArray();
        }

        return [
            'settings' => $exported,
            'theme_settings' => [
                'theme' => $themeKey,
                'values' => $themeSettings,
            ],
            'exported_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Publication / forum taxonomy and lookup tables for child hubs.
     *
     * @return array<string, mixed>
     */
    public function exportMetadataPayload(): array
    {
        $payload = [
            'exported_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        if (Schema::hasTable('thematic_area')) {
            $payload['tables']['thematic_areas'] = ThemeticArea::query()
                ->orderBy('display_order')
                ->orderBy('id')
                ->get(['id', 'description', 'detailed_description', 'display_index', 'display_order', 'icon'])
                ->map(fn ($row) => $this->rowToArray($row))
                ->values()
                ->all();
        }

        if (Schema::hasTable('sub_thematic_area')) {
            $payload['tables']['sub_thematic_areas'] = SubThemeticArea::query()
                ->orderBy('id')
                ->get(['id', 'thematic_area_id', 'description', 'detailed_description', 'icon'])
                ->map(function ($row) {
                    $data = $this->rowToArray($row);
                    $parent = ThemeticArea::find($row->thematic_area_id);
                    $data['parent_description'] = $parent->description ?? null;

                    return $data;
                })
                ->values()
                ->all();
        }

        if (Schema::hasTable('tags')) {
            $payload['tables']['tags'] = Tag::query()
                ->orderBy('tag_text')
                ->get(['id', 'tag_text', 'slug', 'overview', 'is_health_topic', 'is_health_emergency'])
                ->map(fn ($row) => $this->rowToArray($row))
                ->values()
                ->all();
        }

        if (Schema::hasTable('data_categories')) {
            $payload['tables']['resource_types'] = DataCategory::query()
                ->where('is_special', false)
                ->orderBy('category_name')
                ->get(['id', 'category_name', 'slug', 'url_path', 'show_on_menu', 'is_dashboard'])
                ->map(fn ($row) => $this->rowToArray($row))
                ->values()
                ->all();
        }

        if (Schema::hasTable('publication_categories')) {
            $payload['tables']['publication_categories'] = PublicationCategory::query()
                ->orderBy('category_name')
                ->get(['id', 'category_name', 'category_desc', 'parent_id'])
                ->map(function ($row) {
                    $data = $this->rowToArray($row);
                    if ($row->parent_id) {
                        $parent = PublicationCategory::find($row->parent_id);
                        $data['parent_name'] = $parent->category_name ?? null;
                    }

                    return $data;
                })
                ->values()
                ->all();
        }

        if (Schema::hasTable('licenses')) {
            $payload['tables']['licenses'] = License::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'short_name', 'description', 'url', 'sort_order'])
                ->map(fn ($row) => $this->rowToArray($row))
                ->values()
                ->all();
        }

        if (Schema::hasTable('static_links')) {
            $payload['tables']['static_links'] = StaticLink::query()
                ->orderBy('order')
                ->orderBy('title')
                ->get(['id', 'title', 'link', 'order', 'open_in_new_tab'])
                ->map(fn ($row) => $this->rowToArray($row))
                ->values()
                ->all();
        }

        return $payload;
    }

    /**
     * @return array{ok: bool, manifest?: array<string, mixed>, error?: string}
     */
    public function testCentralConnection(string $baseUrl, ?string $apiToken = null): array
    {
        try {
            if ($apiToken) {
                try {
                    app(FederationHubAuthService::class)->bootstrapCentralTokens($baseUrl, $apiToken);
                    $apiToken = null;
                } catch (\Throwable) {
                    // Parent hub may still accept the registration token as a static bearer token.
                }
            }

            $manifest = $this->remoteGet($baseUrl, $apiToken, '/api/federation/manifest');

            return [
                'ok' => true,
                'manifest' => $manifest['manifest'] ?? $manifest,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{branding: array<string, int>, metadata: array<string, int>, manifest: array<string, mixed>}
     */
    public function importFromCentral(string $baseUrl, ?string $apiToken = null, bool $importBranding = true, bool $importMetadata = true): array
    {
        if ($apiToken) {
            try {
                app(FederationHubAuthService::class)->bootstrapCentralTokens($baseUrl, $apiToken);
                $apiToken = null;
            } catch (\Throwable) {
                // Fall back to static bearer token when the parent hub has no OAuth endpoint yet.
            }
        }

        $manifestResponse = $this->remoteGet($baseUrl, $apiToken, '/api/federation/manifest');
        $manifest = $manifestResponse['manifest'] ?? $manifestResponse;

        $summary = [
            'branding' => [],
            'metadata' => [],
            'manifest' => is_array($manifest) ? $manifest : [],
        ];

        if ($importBranding) {
            $brandingResponse = $this->remoteGet($baseUrl, $apiToken, '/api/federation/lookup/settings');
            $branding = $brandingResponse['data'] ?? $brandingResponse;
            $summary['branding'] = $this->importBrandingPayload(is_array($branding) ? $branding : []);
        }

        if ($importMetadata) {
            $metadataResponse = $this->remoteGet($baseUrl, $apiToken, '/api/federation/lookup/metadata');
            $metadata = $metadataResponse['data'] ?? $metadataResponse;
            $summary['metadata'] = $this->importMetadataPayload(is_array($metadata) ? $metadata : []);
        }

        $this->persistCentralHubConnection($baseUrl, $apiToken, $manifest);

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int>
     */
    public function importBrandingPayload(array $payload): array
    {
        $counts = ['settings' => 0, 'theme_settings' => 0];

        if (! Schema::hasTable('setting')) {
            return $counts;
        }

        $setting = DB::table('setting')->where('status', 'active')->first()
            ?? DB::table('setting')->orderBy('id')->first();

        if (! $setting) {
            return $counts;
        }

        $incoming = $payload['settings'] ?? [];
        $updates = [];
        foreach ($incoming as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (! Schema::hasColumn('setting', $key)) {
                continue;
            }
            $updates[$key] = $value;
            $counts['settings']++;
        }

        if ($updates !== []) {
            DB::table('setting')->where('id', $setting->id)->update($updates);
        }

        $themeBlock = $payload['theme_settings'] ?? [];
        $theme = $themeBlock['theme'] ?? null;
        $values = $themeBlock['values'] ?? [];
        if ($theme && is_array($values) && Schema::hasTable('theme_settings')) {
            foreach ($values as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                DB::table('theme_settings')->updateOrInsert(
                    ['theme' => $theme, 'key' => $key],
                    ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
                );
                $counts['theme_settings']++;
            }
        }

        Cache::forget('settings');
        if ($theme) {
            Cache::forget('theme_settings_'.$theme);
        }

        return $counts;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int>
     */
    public function importMetadataPayload(array $payload): array
    {
        $tables = $payload['tables'] ?? $payload;
        $counts = [];

        if (isset($tables['thematic_areas']) && Schema::hasTable('thematic_area')) {
            $counts['thematic_areas'] = $this->importThematicAreas($tables['thematic_areas']);
        }

        if (isset($tables['sub_thematic_areas']) && Schema::hasTable('sub_thematic_area')) {
            $counts['sub_thematic_areas'] = $this->importSubThematicAreas($tables['sub_thematic_areas']);
        }

        if (isset($tables['tags']) && Schema::hasTable('tags')) {
            $counts['tags'] = $this->importTags($tables['tags']);
        }

        if (isset($tables['resource_types']) && Schema::hasTable('data_categories')) {
            $counts['resource_types'] = $this->importResourceTypes($tables['resource_types']);
        }

        if (isset($tables['publication_categories']) && Schema::hasTable('publication_categories')) {
            $counts['publication_categories'] = $this->importPublicationCategories($tables['publication_categories']);
        }

        if (isset($tables['licenses']) && Schema::hasTable('licenses')) {
            $counts['licenses'] = $this->importLicenses($tables['licenses']);
        }

        if (isset($tables['static_links']) && Schema::hasTable('static_links')) {
            $counts['static_links'] = $this->importStaticLinks($tables['static_links']);
        }

        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'central_metadata_synced_at')) {
            DB::table('setting')->where('status', 'active')->update([
                'central_metadata_synced_at' => now(),
            ]);
            Cache::forget('settings');
        }

        return $counts;
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    protected function persistCentralHubConnection(string $baseUrl, ?string $apiToken, array $manifest): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $updates = [
            'central_hub_url' => rtrim($baseUrl, '/'),
            'central_hub_connected_at' => now(),
        ];

        if ($apiToken !== null && Schema::hasColumn('setting', 'central_hub_api_token')) {
            $updates['central_hub_api_token'] = $apiToken;
        }
        if (Schema::hasColumn('setting', 'central_hub_site_id')) {
            $updates['central_hub_site_id'] = $manifest['site_id'] ?? null;
        }

        DB::table('setting')->where('status', 'active')->update($updates);
        Cache::forget('settings');
    }

    /**
     * @return array<string, mixed>
     */
    protected function remoteGet(string $baseUrl, ?string $apiToken, string $path): array
    {
        $url = rtrim($baseUrl, '/').$path;
        $auth = app(FederationHubAuthService::class);

        try {
            return $auth->centralAuthorizedRequest(
                $baseUrl,
                fn ($request) => $request->get($url),
                $apiToken
            );
        } catch (\Throwable $e) {
            if (! $apiToken) {
                throw $e;
            }

            $response = Http::timeout(25)->acceptJson()->withToken($apiToken)->get($url);
            if (! $response->successful()) {
                throw new \RuntimeException('Central hub request failed ('.$response->status().'): '.$response->body());
            }

            $json = $response->json();

            return is_array($json) ? $json : [];
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importThematicAreas(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $description = trim((string) ($row['description'] ?? ''));
            if ($description === '') {
                continue;
            }
            $exists = ThemeticArea::query()->where('description', $description)->exists();
            if ($exists) {
                continue;
            }
            DB::table('thematic_area')->insert([
                'description' => $description,
                'detailed_description' => $row['detailed_description'] ?? null,
                'display_index' => $row['display_index'] ?? null,
                'display_order' => $row['display_order'] ?? 0,
                'icon' => $row['icon'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importSubThematicAreas(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $description = trim((string) ($row['description'] ?? ''));
            if ($description === '') {
                continue;
            }
            if (SubThemeticArea::query()->where('description', $description)->exists()) {
                continue;
            }

            $parentId = null;
            $parentDescription = $row['parent_description'] ?? null;
            if ($parentDescription) {
                $parent = ThemeticArea::query()->where('description', $parentDescription)->first();
                $parentId = $parent->id ?? null;
            }
            if (! $parentId && ! empty($row['thematic_area_id'])) {
                $parentId = (int) $row['thematic_area_id'];
            }
            if (! $parentId) {
                continue;
            }

            DB::table('sub_thematic_area')->insert([
                'thematic_area_id' => $parentId,
                'description' => $description,
                'detailed_description' => $row['detailed_description'] ?? null,
                'icon' => $row['icon'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importTags(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $text = trim((string) ($row['tag_text'] ?? ''));
            if ($text === '') {
                continue;
            }
            if (Tag::query()->where('tag_text', $text)->exists()) {
                continue;
            }
            DB::table('tags')->insert([
                'tag_text' => $text,
                'slug' => $row['slug'] ?? null,
                'overview' => $row['overview'] ?? null,
                'is_health_topic' => (int) ($row['is_health_topic'] ?? 0),
                'is_health_emergency' => (int) ($row['is_health_emergency'] ?? 0),
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importResourceTypes(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $name = trim((string) ($row['category_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            if (DataCategory::query()->where('category_name', $name)->exists()) {
                continue;
            }
            DB::table('data_categories')->insert([
                'category_name' => $name,
                'slug' => $row['slug'] ?? null,
                'url_path' => $row['url_path'] ?? null,
                'show_on_menu' => (int) ($row['show_on_menu'] ?? 0),
                'is_dashboard' => (int) ($row['is_dashboard'] ?? 0),
                'is_special' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importPublicationCategories(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $name = trim((string) ($row['category_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            if (PublicationCategory::query()->where('category_name', $name)->exists()) {
                continue;
            }

            $parentId = null;
            if (! empty($row['parent_name'])) {
                $parent = PublicationCategory::query()->where('category_name', $row['parent_name'])->first();
                $parentId = $parent->id ?? null;
            }

            PublicationCategory::query()->create([
                'category_name' => $name,
                'category_desc' => $row['category_desc'] ?? null,
                'parent_id' => $parentId,
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importLicenses(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            if (License::query()->where('name', $name)->exists()) {
                continue;
            }
            License::query()->create([
                'name' => $name,
                'short_name' => $row['short_name'] ?? null,
                'description' => $row['description'] ?? null,
                'url' => $row['url'] ?? null,
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'is_active' => true,
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function importStaticLinks(array $rows): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            $title = trim((string) ($row['title'] ?? ''));
            $link = trim((string) ($row['link'] ?? ''));
            if ($title === '' || $link === '') {
                continue;
            }
            if (StaticLink::query()->where('title', $title)->where('link', $link)->exists()) {
                continue;
            }
            StaticLink::query()->create([
                'title' => $title,
                'link' => $link,
                'order' => (int) ($row['order'] ?? 0),
                'open_in_new_tab' => (bool) ($row['open_in_new_tab'] ?? false),
            ]);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  object|array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function rowToArray($row): array
    {
        return json_decode(json_encode($row), true) ?: [];
    }

    /**
     * @param  object  $settings
     */
    protected function activeThemeKey($settings): ?string
    {
        $siteTheme = isset($settings->site_theme) ? trim((string) $settings->site_theme) : '';

        if ($siteTheme === 'theme1.') {
            return 'theme1';
        }

        return $siteTheme !== '' ? $siteTheme : null;
    }
}
