<?php

if (! function_exists('africa_map_builtin_versions')) {
    function africa_map_builtin_versions(): array
    {
        return config('maps.versions', []);
    }
}

if (! function_exists('africa_map_custom_versions')) {
    function africa_map_custom_versions(): array
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
                $out[$id] = [
                    'label' => trim((string) ($entry['label'] ?? $id)),
                    'type' => ($entry['type'] ?? 'geojson_script') === 'topojson_url' ? 'topojson_url' : 'geojson_script',
                    'key' => isset($entry['key']) ? trim((string) $entry['key']) : null,
                    'script' => isset($entry['script']) ? trim((string) $entry['script']) : null,
                    'topology_url' => isset($entry['topology_url']) ? trim((string) $entry['topology_url']) : null,
                    'version' => trim((string) ($entry['version'] ?? '1.0')),
                    'join_by' => trim((string) ($entry['join_by'] ?? 'iso-a3')) ?: 'iso-a3',
                    'custom' => true,
                ];
            }

            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (! function_exists('africa_map_all_versions')) {
    function africa_map_all_versions(): array
    {
        return array_merge(africa_map_builtin_versions(), africa_map_custom_versions());
    }
}

if (! function_exists('africa_map_active_version_id')) {
    function africa_map_active_version_id(): string
    {
        $fallback = (string) config('maps.default_version_id', 'africa-prioritisation-1.1.3');
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

if (! function_exists('resolved_africa_map_config')) {
    function resolved_africa_map_config(): array
    {
        $versions = africa_map_all_versions();
        $versionId = africa_map_active_version_id();
        $config = $versions[$versionId] ?? null;
        if (! is_array($config)) {
            $versionId = (string) config('maps.default_version_id', 'africa-prioritisation-1.1.3');
            $config = $versions[$versionId] ?? reset($versions) ?: [];
        }

        $type = ($config['type'] ?? 'geojson_script') === 'topojson_url' ? 'topojson_url' : 'geojson_script';

        return [
            'version_id' => $versionId,
            'label' => $config['label'] ?? $versionId,
            'type' => $type,
            'key' => $config['key'] ?? 'custom/africa',
            'script' => $config['script'] ?? 'assets/js/maps/africa.js',
            'script_url' => isset($config['script']) ? asset($config['script']) : null,
            'topology_url' => $config['topology_url'] ?? null,
            'version' => $config['version'] ?? '1.0',
            'join_by' => $config['join_by'] ?? 'iso-a3',
        ];
    }
}

if (! function_exists('africa_map_settings_for_js')) {
    function africa_map_settings_for_js(): array
    {
        $config = resolved_africa_map_config();

        return [
            'versionId' => $config['version_id'],
            'type' => $config['type'],
            'key' => $config['key'],
            'scriptUrl' => $config['script_url'],
            'topologyUrl' => $config['topology_url'],
            'version' => $config['version'],
            'joinBy' => $config['join_by'],
        ];
    }
}
