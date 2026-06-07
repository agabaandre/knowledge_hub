<?php

namespace App\Repositories;

use App\Models\MapDefinition;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MapsRepository
{
    public function managedDefinitions()
    {
        if (! Schema::hasTable('map_definitions')) {
            return collect();
        }

        return MapDefinition::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    public function find(int $id): ?MapDefinition
    {
        if (! Schema::hasTable('map_definitions')) {
            return null;
        }

        return MapDefinition::query()->find($id);
    }

    public function findBySlug(string $slug): ?MapDefinition
    {
        if (! Schema::hasTable('map_definitions')) {
            return null;
        }

        return MapDefinition::query()->where('slug', $slug)->first();
    }

    public function definitionArray(MapDefinition $record): array
    {
        $sourceType = $record->source_type === 'geojson_script' ? 'geojson_script' : 'topojson_url';
        $topologyUrl = map_build_topology_url([
            'topology_preset' => $record->topology_preset,
            'topology_url' => $record->topology_url,
            'collection_version' => $record->collection_version,
            'country_iso2' => $record->country_iso2,
        ]);

        return [
            'label' => $record->label,
            'description' => $record->description,
            'provider' => $record->provider ?: 'highcharts',
            'type' => $sourceType,
            'key' => $record->map_key,
            'script' => $record->script_path,
            'topology_preset' => $record->topology_preset,
            'topology_url' => $topologyUrl,
            'version' => $record->collection_version ?: config('maps.topology_version', '2.3.3'),
            'join_by' => $record->join_by ?: 'iso-a3',
            'iso_property' => $record->iso_property ?: ($record->join_by ?: 'iso-a3'),
            'scope' => $record->scope ?: 'custom',
            'country_iso2' => $record->country_iso2,
            'managed' => true,
            'managed_id' => $record->id,
            'is_active' => (bool) $record->is_active,
        ];
    }

    public function managedDefinitionEntries(): array
    {
        $entries = [];
        foreach ($this->managedDefinitions() as $record) {
            if (! $record->is_active) {
                continue;
            }
            $entries[$record->slug] = $this->definitionArray($record);
        }

        return $entries;
    }

    public function save(Request $request): MapDefinition
    {
        $record = $request->filled('id')
            ? $this->find((int) $request->input('id'))
            : new MapDefinition();

        if (! $record) {
            $record = new MapDefinition();
        }

        $slugInput = trim((string) $request->input('slug', ''));
        if ($record->exists && $slugInput === '') {
            $slug = $record->slug;
        } else {
            $slug = Str::slug($slugInput !== '' ? $slugInput : trim((string) $request->input('label', '')), '-');
            if ($slug === '') {
                $slug = 'map-'.Str::random(6);
            }
            if (! $record->exists || $slug !== $record->slug) {
                $baseSlug = $slug;
                $counter = 1;
                while (MapDefinition::query()->where('slug', $slug)->where('id', '!=', $record->id ?? 0)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }
            }
        }

        $provider = trim((string) $request->input('provider', 'highcharts'));
        $allowedProviders = array_keys(map_providers());
        if (! in_array($provider, $allowedProviders, true)) {
            $provider = 'highcharts';
        }

        $sourceType = $request->input('source_type', 'topojson_url') === 'geojson_script'
            ? 'geojson_script'
            : 'topojson_url';

        $joinBy = trim((string) $request->input('join_by', 'iso-a3'));
        $allowedJoins = array_keys(map_join_options());
        if (! in_array($joinBy, $allowedJoins, true)) {
            $joinBy = 'iso-a3';
        }

        $record->slug = $slug;
        $record->label = trim((string) $request->input('label'));
        $record->description = trim((string) $request->input('description', '')) ?: null;
        $record->provider = $provider;
        $record->source_type = $sourceType;
        $record->topology_preset = trim((string) $request->input('topology_preset', '')) ?: null;
        $record->topology_url = trim((string) $request->input('topology_url', '')) ?: null;
        $record->collection_version = trim((string) $request->input('collection_version', config('maps.topology_version', '2.3.3')));
        $record->map_key = trim((string) $request->input('map_key', '')) ?: null;
        $record->script_path = trim((string) $request->input('script_path', '')) ?: null;
        $record->join_by = $joinBy;
        $record->iso_property = trim((string) $request->input('iso_property', '')) ?: $joinBy;
        $record->scope = trim((string) $request->input('scope', 'custom')) ?: 'custom';
        $record->country_iso2 = strtoupper(trim((string) $request->input('country_iso2', ''))) ?: null;
        $record->is_active = $request->boolean('is_active', true);
        $record->sort_order = (int) $request->input('sort_order', 0);
        $record->save();

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (! $record) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function assignmentState(): array
    {
        $row = $this->mapSettingsRow();
        $definitions = map_all_definitions();
        $configDefault = (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3');
        $defaultMapId = $configDefault;
        $formDefaultMapId = $configDefault;
        $viewAssignments = [];
        $formViewAssignments = [];
        $showAdminUnitsMap = false;
        $hasDefaultColumn = Schema::hasColumn('setting', 'africa_map_version');
        $hasViewColumn = Schema::hasColumn('setting', 'africa_map_view_versions');
        $hasAdminUnitsColumn = Schema::hasColumn('setting', 'show_admin_units_map');
        $columnsReady = $hasDefaultColumn && $hasViewColumn && $hasAdminUnitsColumn;

        if ($row) {
            if ($hasDefaultColumn && ! empty($row->africa_map_version)) {
                $candidate = trim((string) $row->africa_map_version);
                $formDefaultMapId = $candidate;
                if (isset($definitions[$candidate])) {
                    $defaultMapId = $candidate;
                }
            }

            if ($hasViewColumn && is_string($row->africa_map_view_versions)) {
                $decoded = json_decode($row->africa_map_view_versions, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $context => $mapId) {
                        if (! is_string($mapId) || trim($mapId) === '') {
                            continue;
                        }
                        $mapId = trim($mapId);
                        $formViewAssignments[$context] = $mapId;
                        if (isset($definitions[$mapId])) {
                            $viewAssignments[$context] = $mapId;
                        }
                    }
                }
            }

            if ($hasAdminUnitsColumn) {
                $showAdminUnitsMap = (bool) $row->show_admin_units_map;
            }
        }

        if (! isset($definitions[$defaultMapId])) {
            $defaultMapId = $configDefault;
        }

        return [
            'defaultMapId' => $formDefaultMapId,
            'resolvedDefaultMapId' => $defaultMapId,
            'viewAssignments' => $formViewAssignments,
            'resolvedViewAssignments' => $viewAssignments,
            'showAdminUnitsMap' => $showAdminUnitsMap,
            'columnsReady' => $columnsReady,
            'missingColumns' => array_values(array_filter([
                ! $hasDefaultColumn ? 'africa_map_version' : null,
                ! $hasViewColumn ? 'africa_map_view_versions' : null,
                ! $hasAdminUnitsColumn ? 'show_admin_units_map' : null,
            ])),
            'settingsRowId' => $row->id ?? null,
            'settingsRowTheme' => isset($row->site_theme) ? trim((string) $row->site_theme) : null,
            'rawDefaultMapId' => $row->africa_map_version ?? null,
            'rawViewVersions' => $row->africa_map_view_versions ?? null,
        ];
    }

    /**
     * Resolve which setting row holds map preferences (active row, or any row that has saved map data).
     */
    public function mapSettingsRow(): ?object
    {
        if (! Schema::hasTable('setting')) {
            return null;
        }

        $active = DB::table('setting')->where('status', 'active')->first();

        if ($active && Schema::hasColumn('setting', 'africa_map_version') && ! empty($active->africa_map_version)) {
            return $active;
        }

        if (Schema::hasColumn('setting', 'africa_map_version')) {
            $withMaps = DB::table('setting')
                ->whereNotNull('africa_map_version')
                ->where('africa_map_version', '!=', '')
                ->orderByDesc('id')
                ->first();
            if ($withMaps) {
                return $withMaps;
            }
        }

        return $active ?? DB::table('setting')->orderByDesc('id')->first();
    }

    public function activeSettingRow(): ?Setting
    {
        $row = $this->mapSettingsRow();
        if (! $row) {
            return null;
        }

        return Setting::query()->find($row->id);
    }

    public function saveAssignments(Request $request): bool
    {
        $this->ensureMapAssignmentColumns();

        if (! Schema::hasColumn('setting', 'africa_map_version')) {
            return false;
        }

        if (! DB::table('setting')->exists()) {
            return false;
        }

        $definitions = map_all_definitions();
        $versionId = trim((string) $request->input('default_map_id', ''));
        $payload = [
            'africa_map_version' => isset($definitions[$versionId])
                ? $versionId
                : (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3'),
        ];

        $viewColumnReady = Schema::hasColumn('setting', 'africa_map_view_versions');
        if ($viewColumnReady) {
            $viewVersions = [];
            foreach (array_keys(map_view_context_labels()) as $context) {
                $selected = trim((string) $request->input('view_map_'.$context, ''));
                if ($selected !== '' && isset($definitions[$selected])) {
                    $viewVersions[$context] = $selected;
                }
            }
            $payload['africa_map_view_versions'] = $viewVersions === []
                ? null
                : json_encode($viewVersions);
        }

        if (Schema::hasColumn('setting', 'show_admin_units_map')) {
            $payload['show_admin_units_map'] = $request->boolean('show_admin_units_map') ? 1 : 0;
        }

        // Sync map prefs onto every setting row (default + per-theme rows).
        $updated = DB::table('setting')->update($payload);

        if ($updated >= 0) {
            clear_settings_cache();
        }

        return $updated >= 0 && $viewColumnReady;
    }

    /**
     * Ensure per-view assignment columns exist (handles servers where the first migration ran partially).
     */
    public function ensureMapAssignmentColumns(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        if (Schema::hasColumn('setting', 'africa_map_view_versions')
            && Schema::hasColumn('setting', 'show_admin_units_map')) {
            return;
        }

        Schema::table('setting', function ($table) {
            if (! Schema::hasColumn('setting', 'africa_map_view_versions')) {
                if (Schema::hasColumn('setting', 'africa_map_custom_versions')) {
                    $table->text('africa_map_view_versions')->nullable()->after('africa_map_custom_versions');
                } elseif (Schema::hasColumn('setting', 'africa_map_version')) {
                    $table->text('africa_map_view_versions')->nullable()->after('africa_map_version');
                } else {
                    $table->text('africa_map_view_versions')->nullable();
                }
            }
            if (! Schema::hasColumn('setting', 'show_admin_units_map')) {
                if (Schema::hasColumn('setting', 'africa_map_view_versions')) {
                    $table->boolean('show_admin_units_map')->default(false)->after('africa_map_view_versions');
                } else {
                    $table->boolean('show_admin_units_map')->default(false);
                }
            }
        });
    }
}
