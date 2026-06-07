<?php

namespace App\Services;

use App\Models\MapTopologyVersionHistory;
use App\Repositories\MapsRepository;
use App\Support\MapConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MapTopologyVersionService
{
    public function __construct(
        private MapsRepository $mapsRepository
    ) {}

    public function status(): array
    {
        $current = MapConfig::topologyVersion();
        $latest = MapConfig::latestKnownVersion();
        $checkedAt = MapConfig::lastCheckedAt();

        return [
            'current' => $current,
            'latest' => $latest,
            'update_available' => $latest !== null && version_compare($latest, $current, '>'),
            'checked_at' => $checkedAt,
            'candidates' => $this->versionCandidates(),
            'history' => $this->recentHistory(),
        ];
    }

    /**
     * @return array{current: string, latest: ?string, update_available: bool, checked_at: string}
     */
    public function checkForUpdates(): array
    {
        $current = MapConfig::topologyVersion();
        $latest = $this->probeLatestVersion();
        $checkedAt = now()->toDateTimeString();

        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'map_topology_version_checked_at')) {
            DB::table('setting')->update([
                'map_topology_version_checked_at' => $checkedAt,
                'map_topology_latest_version' => $latest,
            ]);
            MapConfig::clearCache();
            if (function_exists('clear_settings_cache')) {
                clear_settings_cache();
            }
        }

        return [
            'current' => $current,
            'latest' => $latest,
            'update_available' => $latest !== null && version_compare($latest, $current, '>'),
            'checked_at' => $checkedAt,
        ];
    }

    public function applyVersion(string $targetVersion, ?int $userId = null, string $action = 'upgrade'): array
    {
        $targetVersion = trim($targetVersion);
        if ($targetVersion === '' || ! $this->versionExists($targetVersion)) {
            return ['ok' => false, 'message' => 'The selected topology version is not available on Highcharts Map Collection.'];
        }

        $fromVersion = MapConfig::topologyVersion();
        if ($fromVersion === $targetVersion) {
            return ['ok' => false, 'message' => 'This hub is already using topology collection v'.$targetVersion.'.'];
        }

        $snapshot = $this->captureAssignmentsSnapshot();

        DB::transaction(function () use ($fromVersion, $targetVersion, $userId, $action, $snapshot) {
            $this->persistTopologyVersion($targetVersion);
            $this->remapAssignments($fromVersion, $targetVersion);
            $this->updateManagedDefinitions($fromVersion, $targetVersion);

            MapTopologyVersionHistory::query()->create([
                'user_id' => $userId,
                'from_version' => $fromVersion,
                'to_version' => $targetVersion,
                'action' => $action,
                'assignments_snapshot' => $snapshot,
            ]);
        });

        MapConfig::clearCache();
        if (function_exists('clear_settings_cache')) {
            clear_settings_cache();
        }
        MapConfig::applyRuntimeConfig();
        map_clear_definition_cache();

        return [
            'ok' => true,
            'message' => 'Topology collection updated from v'.$fromVersion.' to v'.$targetVersion.'. Map assignments were remapped automatically.',
            'from_version' => $fromVersion,
            'to_version' => $targetVersion,
        ];
    }

    public function revert(int $historyId, ?int $userId = null): array
    {
        $entry = MapTopologyVersionHistory::query()->find($historyId);
        if (! $entry || ! is_array($entry->assignments_snapshot)) {
            return ['ok' => false, 'message' => 'Version history entry not found.'];
        }

        $snapshot = $entry->assignments_snapshot;
        $targetVersion = trim((string) ($snapshot['topology_version'] ?? $entry->from_version));
        $fromVersion = MapConfig::topologyVersion();

        if ($targetVersion === '') {
            return ['ok' => false, 'message' => 'This history entry cannot be restored.'];
        }

        DB::transaction(function () use ($snapshot, $targetVersion, $fromVersion, $userId) {
            $payload = ['map_topology_version' => $targetVersion];
            if (Schema::hasColumn('setting', 'africa_map_version')) {
                $payload['africa_map_version'] = $snapshot['default_map_id'] ?? null;
            }
            if (Schema::hasColumn('setting', 'africa_map_view_versions')) {
                $viewVersions = $snapshot['view_assignments'] ?? null;
                $payload['africa_map_view_versions'] = is_array($viewVersions) && $viewVersions !== []
                    ? json_encode($viewVersions)
                    : null;
            }

            DB::table('setting')->update($payload);
            $this->updateManagedDefinitions($fromVersion, $targetVersion);

            MapTopologyVersionHistory::query()->create([
                'user_id' => $userId,
                'from_version' => $fromVersion,
                'to_version' => $targetVersion,
                'action' => 'revert',
                'assignments_snapshot' => $this->captureAssignmentsSnapshot(),
            ]);
        });

        MapConfig::clearCache();
        if (function_exists('clear_settings_cache')) {
            clear_settings_cache();
        }
        MapConfig::applyRuntimeConfig();
        map_clear_definition_cache();

        return [
            'ok' => true,
            'message' => 'Restored topology collection v'.$targetVersion.' and previous map assignments.',
            'to_version' => $targetVersion,
        ];
    }

    /**
     * @return list<string>
     */
    public function versionCandidates(): array
    {
        $candidates = config('maps.topology_version_candidates', ['2.3.3']);
        $candidates = is_array($candidates) ? $candidates : ['2.3.3'];
        $current = MapConfig::topologyVersion();
        if (! in_array($current, $candidates, true)) {
            $candidates[] = $current;
        }

        usort($candidates, static fn (string $a, string $b): int => version_compare($b, $a));

        return array_values(array_unique($candidates));
    }

    public function probeLatestVersion(): ?string
    {
        foreach ($this->versionCandidates() as $version) {
            if ($this->versionExists($version)) {
                return $version;
            }
        }

        return null;
    }

    public function versionExists(string $version): bool
    {
        $version = trim($version);
        if ($version === '') {
            return false;
        }

        $url = str_replace(
            ['{version}'],
            [$version],
            (string) config('maps.topology_probe_url', 'https://code.highcharts.com/mapdata/{version}/custom/world.topo.json')
        );

        try {
            $response = Http::timeout(8)->head($url);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function recentHistory(int $limit = 10): array
    {
        if (! Schema::hasTable('map_topology_version_history')) {
            return [];
        }

        return MapTopologyVersionHistory::query()
            ->with('user:id,name,email')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(static function (MapTopologyVersionHistory $row): array {
                return [
                    'id' => $row->id,
                    'from_version' => $row->from_version,
                    'to_version' => $row->to_version,
                    'action' => $row->action,
                    'user' => $row->user?->name ?? $row->user?->email,
                    'created_at' => $row->created_at?->toDateTimeString(),
                ];
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function captureAssignmentsSnapshot(): array
    {
        $state = $this->mapsRepository->assignmentState();

        return [
            'topology_version' => MapConfig::topologyVersion(),
            'default_map_id' => $state['defaultMapId'] ?? null,
            'view_assignments' => $state['viewAssignments'] ?? [],
        ];
    }

    private function persistTopologyVersion(string $version): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'map_topology_version')) {
            return;
        }

        DB::table('setting')->update([
            'map_topology_version' => $version,
            'map_topology_latest_version' => $version,
            'map_topology_version_checked_at' => now(),
        ]);
    }

    private function remapAssignments(string $fromVersion, string $toVersion): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $payload = [];

        if (Schema::hasColumn('setting', 'africa_map_version')) {
            $current = trim((string) (DB::table('setting')->value('africa_map_version') ?? ''));
            if ($current !== '') {
                $payload['africa_map_version'] = map_remap_definition_id($current, $fromVersion, $toVersion);
            }
        }

        if (Schema::hasColumn('setting', 'africa_map_view_versions')) {
            $raw = DB::table('setting')->value('africa_map_view_versions');
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($decoded)) {
                $remapped = [];
                foreach ($decoded as $context => $mapId) {
                    if (! is_string($mapId) || trim($mapId) === '') {
                        continue;
                    }
                    $remapped[$context] = map_remap_definition_id(trim($mapId), $fromVersion, $toVersion);
                }
                $payload['africa_map_view_versions'] = $remapped === [] ? null : json_encode($remapped);
            }
        }

        if ($payload !== []) {
            DB::table('setting')->update($payload);
        }
    }

    private function updateManagedDefinitions(string $fromVersion, string $toVersion): void
    {
        if (! Schema::hasTable('map_definitions')) {
            return;
        }

        $records = DB::table('map_definitions')->get(['id', 'slug', 'collection_version', 'topology_url']);
        foreach ($records as $record) {
            $updates = [];
            $collectionVersion = trim((string) ($record->collection_version ?? ''));
            if ($collectionVersion === '' || $collectionVersion === $fromVersion) {
                $updates['collection_version'] = $toVersion;
            }

            $topologyUrl = (string) ($record->topology_url ?? '');
            if ($topologyUrl !== '' && str_contains($topologyUrl, '/mapdata/'.$fromVersion.'/')) {
                $updates['topology_url'] = str_replace('/mapdata/'.$fromVersion.'/', '/mapdata/'.$toVersion.'/', $topologyUrl);
            }

            $slug = (string) ($record->slug ?? '');
            $newSlug = map_remap_definition_id($slug, $fromVersion, $toVersion);
            if ($newSlug !== $slug) {
                $updates['slug'] = $newSlug;
            }

            if ($updates !== []) {
                DB::table('map_definitions')->where('id', $record->id)->update($updates);
            }
        }
    }
}
