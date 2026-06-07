<?php

namespace App\Repositories;

use App\Models\MapDefinition;
use Illuminate\Http\Request;
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

    public function saveAssignments(Request $request): void
    {
        $settings = settings();
        if (! $settings) {
            return;
        }

        if (Schema::hasColumn('setting', 'africa_map_version')) {
            $versionId = trim((string) $request->input('default_map_id', ''));
            $allowed = array_keys(map_all_definitions());
            $settings->africa_map_version = in_array($versionId, $allowed, true)
                ? $versionId
                : (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3');
        }

        if (Schema::hasColumn('setting', 'africa_map_view_versions')) {
            $viewVersions = [];
            foreach (array_keys(map_view_context_labels()) as $context) {
                $selected = trim((string) $request->input('view_map_'.$context, ''));
                if ($selected !== '') {
                    $viewVersions[$context] = $selected;
                }
            }
            $settings->africa_map_view_versions = $viewVersions === []
                ? null
                : json_encode($viewVersions);
        }

        if (Schema::hasColumn('setting', 'show_admin_units_map')) {
            $settings->show_admin_units_map = $request->boolean('show_admin_units_map');
        }

        $settings->save();
    }
}
