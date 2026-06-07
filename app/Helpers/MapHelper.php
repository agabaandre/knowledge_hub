<?php

use App\Models\Country;
use App\Repositories\MapsRepository;

if (! function_exists('map_providers')) {
    function map_providers(): array
    {
        return config('maps.providers', []);
    }
}

if (! function_exists('map_topology_presets')) {
    function map_topology_presets(): array
    {
        return config('maps.topology_presets', []);
    }
}

if (! function_exists('map_join_options')) {
    function map_join_options(): array
    {
        return [
            'iso-a3' => 'ISO 3166-1 alpha-3 (iso-a3)',
            'hc-key' => 'Highcharts key (hc-key, usually ISO alpha-2)',
            'iso-a2' => 'ISO 3166-1 alpha-2 (iso-a2)',
            'name' => 'Region name',
        ];
    }
}

if (! function_exists('map_view_context_labels')) {
    function map_view_context_labels(): array
    {
        return [
            'frontend_countries' => 'Frontend member states map',
            'admin_metrics' => 'Admin dashboard metrics map',
            'admin_visits' => 'Admin dashboard visits map (worldwide)',
            'admin_rcc' => 'Admin RCC dashboard map',
            'frontend_admin_units' => 'Frontend admin units map',
            'country_hub' => 'Country hub map (owner country)',
        ];
    }
}

if (! function_exists('map_african_countries_catalog')) {
    function map_african_countries_catalog(): array
    {
        $countries = config('maps_african_countries.countries', []);

        return is_array($countries) ? $countries : [];
    }
}

if (! function_exists('map_country_definition_id')) {
    function map_country_definition_id(string $iso2): string
    {
        $iso2 = strtolower(trim($iso2));
        $version = str_replace('.', '-', (string) config('maps.topology_version', '2.3.3'));

        return 'country-'.$iso2.'-topo-'.$version;
    }
}

if (! function_exists('map_country_topology_filename')) {
    function map_country_topology_filename(string $iso2): string
    {
        $iso2 = strtolower(trim($iso2));
        $catalog = map_african_countries_catalog();
        $meta = $catalog[$iso2] ?? [];

        if (! empty($meta['topo_file'])) {
            return (string) $meta['topo_file'];
        }

        return $iso2.'-all.topo.json';
    }
}

if (! function_exists('map_country_topology_url')) {
    function map_country_topology_url(string $iso2, ?string $topoFile = null): string
    {
        $iso2 = strtolower(trim($iso2));
        $version = (string) config('maps.topology_version', '2.3.3');
        $base = str_replace('{version}', $version, (string) config('maps.topology_base_url', 'https://code.highcharts.com/mapdata/{version}/'));
        $filename = $topoFile ?: map_country_topology_filename($iso2);

        return rtrim($base, '/').'/countries/'.$iso2.'/'.ltrim($filename, '/');
    }
}

if (! function_exists('map_african_country_definitions')) {
    function map_african_country_definitions(): array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $version = (string) config('maps.topology_version', '2.3.3');
        $definitions = [];

        foreach (map_african_countries_catalog() as $iso2 => $meta) {
            if (! is_array($meta)) {
                continue;
            }
            $iso2 = strtolower(trim((string) $iso2));
            if ($iso2 === '') {
                continue;
            }

            $label = trim((string) ($meta['label'] ?? strtoupper($iso2)));
            $definitionId = map_country_definition_id($iso2);

            $definitions[$definitionId] = normalize_map_definition_config($definitionId, [
                'label' => $label.' — Highcharts TopoJSON v'.$version,
                'provider' => 'highcharts',
                'type' => 'topojson_url',
                'topology_preset' => 'country-admin1',
                'topology_url' => map_country_topology_url($iso2, $meta['topo_file'] ?? null),
                'version' => $version,
                'join_by' => 'iso-a3',
                'scope' => 'country',
                'country_iso2' => $iso2,
                'builtin' => true,
            ]);
        }

        return $cached = $definitions;
    }
}

if (! function_exists('map_country_hub_auto_contexts')) {
    function map_country_hub_auto_contexts(): array
    {
        $contexts = config('maps.country_hub.auto_contexts', []);

        return is_array($contexts) ? $contexts : [];
    }
}

if (! function_exists('map_country_definition_id_for_country')) {
    function map_country_definition_id_for_country(?Country $country): ?string
    {
        if (! $country || empty($country->iso_code)) {
            return null;
        }

        $iso2 = strtolower(trim((string) $country->iso_code));
        if ($iso2 === '') {
            return null;
        }

        $definitionId = map_country_definition_id($iso2);

        return isset(map_african_country_definitions()[$definitionId]) ? $definitionId : null;
    }
}

if (! function_exists('map_auto_country_hub_version_id')) {
    function map_auto_country_hub_version_id(?string $context): ?string
    {
        if ($context === null || $context === '' || ! in_array($context, map_country_hub_auto_contexts(), true)) {
            return null;
        }

        if (! function_exists('hub_admin_units_enabled') || ! hub_admin_units_enabled()) {
            return null;
        }

        $country = hub_owner_country();
        if (! $country) {
            return null;
        }

        return map_country_definition_id_for_country($country);
    }
}

if (! function_exists('map_country_map_config_for_country')) {
    function map_country_map_config_for_country(?Country $country): ?array
    {
        if (! $country || empty($country->iso_code)) {
            return null;
        }

        $iso2 = strtolower(trim((string) $country->iso_code));
        if ($iso2 === '') {
            return null;
        }

        $definitionId = map_country_definition_id_for_country($country);
        $topologyUrl = map_country_topology_url($iso2);

        return [
            'country_id' => (int) $country->id,
            'country_name' => $country->name,
            'iso2' => $iso2,
            'definition_id' => $definitionId,
            'provider' => config('maps.admin_units.provider', 'highcharts'),
            'type' => 'topojson_url',
            'topology_url' => $topologyUrl,
            'version' => config('maps.topology_version', '2.3.3'),
            'join_by' => config('maps.admin_units.join_by', 'iso-a3'),
            'iso_property' => config('maps.admin_units.join_by', 'iso-a3'),
        ];
    }
}

if (! function_exists('map_country_map_settings_for_js')) {
    function map_country_map_settings_for_js(?Country $country, ?string $context = 'country_hub'): ?array
    {
        $config = map_country_map_config_for_country($country);
        if (! $config) {
            return null;
        }

        return [
            'context' => $context,
            'versionId' => $config['definition_id'] ?: ('country-'.$config['iso2']),
            'provider' => $config['provider'] ?? 'highcharts',
            'type' => $config['type'],
            'key' => null,
            'scriptUrl' => null,
            'topologyUrl' => $config['topology_url'],
            'version' => $config['version'],
            'joinBy' => $config['join_by'],
            'isoProperty' => $config['iso_property'] ?? $config['join_by'],
            'countryName' => $config['country_name'],
            'countryIso2' => $config['iso2'],
        ];
    }
}

if (! function_exists('federated_hub_map_settings_for_js')) {
    function federated_hub_map_settings_for_js($hub): ?array
    {
        if (! $hub || ! method_exists($hub, 'mappedCountry')) {
            return null;
        }

        $country = $hub->mappedCountry;
        if (! $country) {
            return null;
        }

        return map_country_map_settings_for_js($country, 'country_hub');
    }
}

if (! function_exists('map_build_topology_url')) {
    function map_build_topology_url(array $definition): ?string
    {
        if (! empty($definition['topology_url'])) {
            return (string) $definition['topology_url'];
        }

        $preset = trim((string) ($definition['topology_preset'] ?? ''));
        if ($preset === '') {
            return null;
        }

        $version = trim((string) ($definition['collection_version'] ?? $definition['version'] ?? config('maps.topology_version', '2.3.3')));
        $base = str_replace('{version}', $version, (string) config('maps.topology_base_url', 'https://code.highcharts.com/mapdata/{version}/'));
        $path = map_topology_presets()[$preset] ?? null;

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_contains($path, '{iso2}')) {
            $iso2 = strtolower(trim((string) ($definition['country_iso2'] ?? '')));
            if ($iso2 === '') {
                return null;
            }
            $path = str_replace('{iso2}', $iso2, $path);
        }

        return rtrim($base, '/').'/'.ltrim($path, '/');
    }
}

if (! function_exists('map_builtin_definitions')) {
    function map_builtin_definitions(): array
    {
        $builtins = config('maps.versions', []);
        $normalized = [];

        foreach ($builtins as $id => $config) {
            if (! is_array($config)) {
                continue;
            }
            $normalized[$id] = normalize_map_definition_config($id, $config + ['builtin' => true]);
        }

        return $normalized;
    }
}

if (! function_exists('africa_map_builtin_versions')) {
    function africa_map_builtin_versions(): array
    {
        return map_builtin_definitions();
    }
}

if (! function_exists('map_legacy_custom_definitions')) {
    function map_legacy_custom_definitions(): array
    {
        try {
            $settings = function_exists('settings') ? settings() : null;
            if (! $settings || ! \Illuminate\Support\Facades\Schema::hasColumn('setting', 'africa_map_custom_versions')) {
                return [];
            }
            $raw = $settings->africa_map_custom_versions ?? '';
            if (! is_string($raw) || trim($raw) === '') {
                return [];
            }
            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                return [];
            }
            $out = [];
            foreach ($decoded as $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                $id = trim((string) ($entry['id'] ?? ''));
                if ($id === '') {
                    continue;
                }
                $out[$id] = normalize_map_definition_config($id, [
                    'label' => trim((string) ($entry['label'] ?? $id)),
                    'provider' => $entry['provider'] ?? 'highcharts',
                    'type' => ($entry['type'] ?? 'geojson_script') === 'topojson_url' ? 'topojson_url' : 'geojson_script',
                    'key' => isset($entry['key']) ? trim((string) $entry['key']) : null,
                    'script' => isset($entry['script']) ? trim((string) $entry['script']) : null,
                    'topology_url' => isset($entry['topology_url']) ? trim((string) $entry['topology_url']) : null,
                    'topology_preset' => isset($entry['topology_preset']) ? trim((string) $entry['topology_preset']) : null,
                    'version' => trim((string) ($entry['version'] ?? '1.0')),
                    'join_by' => trim((string) ($entry['join_by'] ?? 'iso-a3')) ?: 'iso-a3',
                    'iso_property' => trim((string) ($entry['iso_property'] ?? $entry['join_by'] ?? 'iso-a3')),
                    'legacy' => true,
                ]);
            }

            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (! function_exists('africa_map_custom_versions')) {
    function africa_map_custom_versions(): array
    {
        return array_merge(
            map_managed_definitions(),
            map_legacy_custom_definitions()
        );
    }
}

if (! function_exists('map_managed_definitions')) {
    function map_managed_definitions(): array
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('map_definitions')) {
                return [];
            }

            return app(MapsRepository::class)->managedDefinitionEntries();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (! function_exists('map_all_definitions')) {
    function map_all_definitions(): array
    {
        return array_merge(
            map_builtin_definitions(),
            map_african_country_definitions(),
            map_managed_definitions(),
            map_legacy_custom_definitions()
        );
    }
}

if (! function_exists('africa_map_all_versions')) {
    function africa_map_all_versions(): array
    {
        return map_all_definitions();
    }
}

if (! function_exists('map_view_versions_from_settings')) {
    function map_view_versions_from_settings(): array
    {
        try {
            $settings = function_exists('settings') ? settings() : null;
            if (! $settings || ! \Illuminate\Support\Facades\Schema::hasColumn('setting', 'africa_map_view_versions')) {
                return [];
            }
            $raw = $settings->africa_map_view_versions ?? '';
            if (! is_string($raw) || trim($raw) === '') {
                return [];
            }
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (! function_exists('africa_map_active_version_id')) {
    function africa_map_active_version_id(): string
    {
        $fallback = (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3');
        try {
            $settings = function_exists('settings') ? settings() : null;
            if ($settings && \Illuminate\Support\Facades\Schema::hasColumn('setting', 'africa_map_version')) {
                $selected = trim((string) ($settings->africa_map_version ?? ''));
                if ($selected !== '') {
                    return $selected;
                }
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return $fallback;
    }
}

if (! function_exists('map_version_for_context')) {
    function map_version_for_context(?string $context): string
    {
        $global = africa_map_active_version_id();
        $definitions = map_all_definitions();

        if ($context !== null && $context !== '') {
            $fromSettings = map_view_versions_from_settings();
            if (! empty($fromSettings[$context])) {
                $selected = trim((string) $fromSettings[$context]);
                if (isset($definitions[$selected])) {
                    return $selected;
                }
            }

            $fromConfig = config("maps.views.{$context}");
            if (is_string($fromConfig) && $fromConfig !== '' && isset($definitions[$fromConfig])) {
                return $fromConfig;
            }

            $autoCountry = map_auto_country_hub_version_id($context);
            if ($autoCountry && isset($definitions[$autoCountry])) {
                return $autoCountry;
            }
        }

        return isset($definitions[$global]) ? $global : (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3');
    }
}

if (! function_exists('normalize_map_definition_config')) {
    function normalize_map_definition_config(string $definitionId, array $config): array
    {
        $type = ($config['type'] ?? 'geojson_script') === 'topojson_url' ? 'topojson_url' : 'geojson_script';
        $joinBy = trim((string) ($config['join_by'] ?? 'iso-a3')) ?: 'iso-a3';
        $topologyUrl = map_build_topology_url([
            'topology_url' => $config['topology_url'] ?? null,
            'topology_preset' => $config['topology_preset'] ?? null,
            'collection_version' => $config['version'] ?? $config['collection_version'] ?? config('maps.topology_version', '2.3.3'),
            'country_iso2' => $config['country_iso2'] ?? null,
        ]);

        return [
            'version_id' => $definitionId,
            'label' => $config['label'] ?? $definitionId,
            'description' => $config['description'] ?? null,
            'provider' => $config['provider'] ?? 'highcharts',
            'type' => $type,
            'key' => $config['key'] ?? 'custom/africa',
            'script' => $config['script'] ?? 'assets/js/maps/africa.js',
            'script_url' => isset($config['script']) ? asset($config['script']) : null,
            'topology_preset' => $config['topology_preset'] ?? null,
            'topology_url' => $topologyUrl,
            'version' => $config['version'] ?? $config['collection_version'] ?? config('maps.topology_version', '2.3.3'),
            'join_by' => $joinBy,
            'iso_property' => $config['iso_property'] ?? $joinBy,
            'scope' => $config['scope'] ?? 'custom',
            'country_iso2' => $config['country_iso2'] ?? null,
            'builtin' => ! empty($config['builtin']),
            'managed' => ! empty($config['managed']),
            'legacy' => ! empty($config['legacy']),
        ];
    }
}

if (! function_exists('normalize_map_version_config')) {
    function normalize_map_version_config(string $versionId, array $config): array
    {
        return normalize_map_definition_config($versionId, $config);
    }
}

if (! function_exists('resolved_map_config')) {
    function resolved_map_config(?string $context = null, ?string $definitionId = null): array
    {
        $definitions = map_all_definitions();
        $versionId = $definitionId ?: map_version_for_context($context);
        $config = $definitions[$versionId] ?? null;

        if (! is_array($config)) {
            $versionId = (string) config('maps.default_version_id', 'africa-sadr-topo-2.3.3');
            $raw = $definitions[$versionId] ?? reset($definitions) ?: [];
            $config = is_array($raw) ? $raw : [];
        }

        if (isset($config['version_id'])) {
            return $config;
        }

        return normalize_map_definition_config($versionId, $config);
    }
}

if (! function_exists('resolved_africa_map_config')) {
    function resolved_africa_map_config(?string $context = null): array
    {
        return resolved_map_config($context);
    }
}

if (! function_exists('map_settings_for_js')) {
    function map_settings_for_js(?string $context = null): array
    {
        $config = resolved_map_config($context);

        return [
            'context' => $context,
            'versionId' => $config['version_id'],
            'provider' => $config['provider'] ?? 'highcharts',
            'type' => $config['type'],
            'key' => $config['key'],
            'scriptUrl' => $config['script_url'],
            'topologyUrl' => $config['topology_url'],
            'topologyPreset' => $config['topology_preset'] ?? null,
            'version' => $config['version'],
            'joinBy' => $config['join_by'],
            'isoProperty' => $config['iso_property'] ?? $config['join_by'],
            'scope' => $config['scope'] ?? 'custom',
        ];
    }
}

if (! function_exists('africa_map_settings_for_js')) {
    function africa_map_settings_for_js(?string $context = null): array
    {
        return map_settings_for_js($context);
    }
}

if (! function_exists('map_join_property')) {
    function map_join_property(?string $context = null): string
    {
        return resolved_map_config($context)['join_by'] ?? 'iso-a3';
    }
}

if (! function_exists('map_point_from_country')) {
    function map_point_from_country(object $country, array $extra = [], ?string $context = null): array
    {
        $iso2 = strtolower(trim((string) ($country->iso_code ?? '')));
        $iso3 = strtoupper(trim((string) ($country->iso3_code ?? '')));
        if ($iso3 === '' && $iso2 !== '') {
            $iso3 = map_iso3_from_iso2($iso2);
        }

        $name = trim((string) ($extra['name'] ?? $country->name ?? ''));
        $mapName = config('maps.choropleth_map_name_aliases.'.$name, $name);

        return array_merge($extra, [
            'name' => $mapName,
            'country_name' => $country->name ?? $name,
            'hc-key' => $iso2,
            'iso-a3' => $iso3,
            'iso-a2' => strtoupper($iso2),
        ]);
    }
}

if (! function_exists('map_iso3_from_iso2')) {
    function map_iso3_from_iso2(string $iso2): string
    {
        $iso2 = strtoupper(trim($iso2));
        if ($iso2 === '') {
            return '';
        }

        static $catalog = null;
        if ($catalog === null) {
            $catalog = [];
            foreach (map_african_countries_catalog() as $code => $meta) {
                if (! is_array($meta)) {
                    continue;
                }
                $catalog[strtoupper((string) $code)] = strtoupper((string) ($meta['iso3'] ?? ''));
            }
            $catalog += [
                'SO' => 'SOM',
                'EH' => 'ESH',
                'SX' => '-99',
            ];
        }

        if (! empty($catalog[$iso2])) {
            return $catalog[$iso2];
        }

        return strtoupper((string) (Country::query()->where('iso_code', $iso2)->value('iso3_code') ?? ''));
    }
}

if (! function_exists('map_expand_choropleth_points')) {
    /**
     * Add data points for map territories that use non-standard join keys (e.g. Somaliland).
     *
     * @param  list<array<string, mixed>>  $points
     * @return list<array<string, mixed>>
     */
    function map_expand_choropleth_points(array $points): array
    {
        $aliases = config('maps.choropleth_territory_aliases', []);
        if (! is_array($aliases) || $aliases === []) {
            return $points;
        }

        $byIso3 = [];
        foreach ($points as $point) {
            $iso3 = strtoupper(trim((string) ($point['iso-a3'] ?? '')));
            if ($iso3 !== '') {
                $byIso3[$iso3] = $point;
            }
        }

        foreach ($aliases as $alias) {
            if (! is_array($alias)) {
                continue;
            }
            $sourceIso3 = strtoupper(trim((string) ($alias['inherit_iso3'] ?? '')));
            if ($sourceIso3 === '' || empty($byIso3[$sourceIso3])) {
                continue;
            }

            $source = $byIso3[$sourceIso3];
            $clone = $source;
            $clone['name'] = (string) ($alias['label'] ?? $clone['name'] ?? '');
            $clone['territory_alias'] = true;
            if (! empty($alias['iso-a3'])) {
                $clone['iso-a3'] = (string) $alias['iso-a3'];
            }
            if (! empty($alias['hc-key'])) {
                $clone['hc-key'] = strtolower((string) $alias['hc-key']);
            }
            if (! empty($alias['iso-a2'])) {
                $clone['iso-a2'] = strtoupper((string) $alias['iso-a2']);
            }
            unset($clone['country_id'], $clone['detail_url']);
            $points[] = $clone;
        }

        return $points;
    }
}

if (! function_exists('map_highcharts_join_by')) {
    /**
     * Highcharts joinBy option with ISO key fallbacks for disputed territories.
     */
    function map_highcharts_join_by(?string $context = null): array
    {
        $primary = map_join_property($context);

        if ($primary === 'hc-key') {
            return [['hc-key', 'hc-key'], ['iso-a3', 'iso-a3']];
        }

        return [['iso-a3', 'iso-a3'], ['hc-key', 'hc-key']];
    }
}

if (! function_exists('show_admin_units_map_enabled')) {
    function show_admin_units_map_enabled(): bool
    {
        try {
            $settings = function_exists('settings') ? settings() : null;
            if ($settings && \Illuminate\Support\Facades\Schema::hasColumn('setting', 'show_admin_units_map')) {
                return (bool) $settings->show_admin_units_map;
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return (bool) config('maps.admin_units.show_on_frontend', false);
    }
}

if (! function_exists('admin_units_map_enabled')) {
    function admin_units_map_enabled(): bool
    {
        return function_exists('hub_admin_units_enabled')
            && hub_admin_units_enabled()
            && show_admin_units_map_enabled();
    }
}

if (! function_exists('hub_owner_country')) {
    function hub_owner_country(): ?Country
    {
        $countryId = function_exists('hub_owner_country_id') ? hub_owner_country_id() : null;
        if (! $countryId) {
            return null;
        }

        return Country::query()->find($countryId);
    }
}

if (! function_exists('resolved_admin_units_map_config')) {
    function resolved_admin_units_map_config(): ?array
    {
        return map_country_map_config_for_country(hub_owner_country());
    }
}

if (! function_exists('admin_units_map_settings_for_js')) {
    function admin_units_map_settings_for_js(): ?array
    {
        if (! admin_units_map_enabled()) {
            return null;
        }

        return map_country_map_settings_for_js(hub_owner_country(), 'frontend_admin_units');
    }
}
