<?php

$topologyVersion = env('MAP_TOPOLOGY_VERSION', '2.3.3');
$topologyBase = 'https://code.highcharts.com/mapdata/'.$topologyVersion.'/';

return [

    'default_version_id' => env('AFRICA_MAP_VERSION', 'africa-sadr-topo-2.3.3'),

    'topology_version' => $topologyVersion,

    'topology_base_url' => env('MAP_TOPOLOGY_BASE_URL', 'https://code.highcharts.com/mapdata/{version}/'),

    'topology_probe_url' => 'https://code.highcharts.com/mapdata/{version}/custom/world.topo.json',

    /*
    | Known Highcharts Map Collection releases (newest first). Used when checking for updates.
    */
    'topology_version_candidates' => [
        '2.3.3',
        '2.3.0',
        '2.2.0',
        '2.1.0',
        '2.0.0',
        '1.1.3',
    ],

    /*
    | Rendering engines that consume ISO-keyed TopoJSON (Highcharts Map Collection format).
    */
    'providers' => [
        'highcharts' => [
            'label' => 'Highcharts Maps',
            'description' => 'Native Highcharts mapChart choropleths.',
            'supports_script_maps' => true,
        ],
        'fusion' => [
            'label' => 'FusionCharts',
            'description' => 'FusionMaps using the same TopoJSON / ISO property joins.',
            'supports_script_maps' => false,
        ],
        'amcharts' => [
            'label' => 'amCharts',
            'description' => 'amCharts map polygons from TopoJSON with ISO joins.',
            'supports_script_maps' => false,
        ],
        'generic_topojson' => [
            'label' => 'Generic TopoJSON',
            'description' => 'Provider-agnostic ISO choropleth data layer.',
            'supports_script_maps' => false,
        ],
    ],

    /*
    | Highcharts Map Collection paths (relative to topology_base_url + version).
    */
    'topology_presets' => [
        'africa-sadr' => 'custom/africa-sadr.topo.json',
        'africa' => 'custom/africa.topo.json',
        'world-highres' => 'custom/world-highres.topo.json',
        'world' => 'custom/world.topo.json',
        'world-continents' => 'custom/world-continents.topo.json',
        'europe' => 'custom/europe.topo.json',
        'country-admin1' => 'countries/{iso2}/{iso2}-all.topo.json',
    ],

    'views' => [
        'frontend_countries' => env('MAP_VIEW_FRONTEND_COUNTRIES'),
        'admin_metrics' => env('MAP_VIEW_ADMIN_METRICS'),
        'admin_visits' => env('MAP_VIEW_ADMIN_VISITS', 'world-visits-topo-2.3.3'),
        'admin_rcc' => env('MAP_VIEW_ADMIN_RCC'),
        'frontend_admin_units' => env('MAP_VIEW_FRONTEND_ADMIN_UNITS'),
        'country_hub' => env('MAP_VIEW_COUNTRY_HUB'),
    ],

    /*
    | When a country hub is configured (admin units + owner country), these view
    | contexts auto-resolve to that country's Highcharts map unless overridden in Maps Management.
    */
    'country_hub' => [
        'auto_contexts' => [
            'frontend_countries',
            'country_hub',
            'frontend_admin_units',
        ],
    ],

    /*
    | Built-in map definitions that do not follow the active topology collection version.
    */
    'static_versions' => [
        'africa-prioritisation-1.1.3' => [
            'label' => 'Africa (Western Sahara separate) — Prioritisation v1.1.3',
            'provider' => 'highcharts',
            'type' => 'geojson_script',
            'key' => 'custom/africa',
            'script' => 'assets/js/maps/africa.js',
            'version' => '1.1.3',
            'join_by' => 'iso-a3',
            'scope' => 'africa',
        ],
    ],

    /*
    | Built-in TopoJSON maps generated from the active topology collection version.
    | Slug patterns use {version} (e.g. africa-sadr-topo-2.3.3).
    */
    'version_templates' => [
        [
            'slug' => 'africa-sadr-topo-{version}',
            'label' => 'Africa SADR — Highcharts TopoJSON v{version}',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'africa-sadr',
            'join_by' => 'hc-key',
            'scope' => 'africa',
        ],
        [
            'slug' => 'world-highres-topo-{version}',
            'label' => 'World high resolution — Highcharts TopoJSON v{version}',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'world-highres',
            'join_by' => 'iso-a3',
            'scope' => 'world',
        ],
        [
            'slug' => 'world-topo-{version}',
            'label' => 'World — Highcharts TopoJSON v{version}',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'world',
            'join_by' => 'iso-a3',
            'scope' => 'world',
        ],
        [
            'slug' => 'world-visits-topo-{version}',
            'label' => 'World (portal visits) — Highcharts TopoJSON v{version}',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'world',
            'join_by' => 'hc-key',
            'iso_property' => 'hc-key',
            'scope' => 'world',
        ],
        [
            'slug' => 'fusion-africa-sadr-{version}',
            'label' => 'Africa SADR — FusionCharts (HC TopoJSON v{version})',
            'provider' => 'fusion',
            'type' => 'topojson_url',
            'topology_preset' => 'africa-sadr',
            'join_by' => 'iso-a3',
            'iso_property' => 'iso-a3',
            'scope' => 'africa',
        ],
        [
            'slug' => 'fusion-world-highres-{version}',
            'label' => 'World high res — FusionCharts (HC TopoJSON v{version})',
            'provider' => 'fusion',
            'type' => 'topojson_url',
            'topology_preset' => 'world-highres',
            'join_by' => 'iso-a3',
            'iso_property' => 'iso-a3',
            'scope' => 'world',
        ],
    ],

    // Legacy alias kept for backward compatibility.
    'versions' => [],

    'admin_units' => [
        'topology_preset' => 'country-admin1',
        'topology_url_template' => env(
            'ADMIN_UNITS_MAP_TOPOLOGY_URL',
            'https://code.highcharts.com/mapdata/'.$topologyVersion.'/countries/{iso2}/{iso2}-all.topo.json'
        ),
        'version' => $topologyVersion,
        'join_by' => env('ADMIN_UNITS_MAP_JOIN_BY', 'iso-a3'),
        'provider' => env('ADMIN_UNITS_MAP_PROVIDER', 'highcharts'),
    ],

    /*
    | Extra map polygons (e.g. Somaliland on the prioritisation Africa map) use non-standard
    | ISO keys. Duplicate indicator data onto these keys when the source member state has data.
    */
    'choropleth_territory_aliases' => [
        [
            'label' => 'Somaliland',
            'inherit_iso3' => 'SOM',
            'iso-a3' => '-99',
            'hc-key' => 'sx',
            'iso-a2' => 'SX',
        ],
    ],

    /*
    | Map feature names that differ from member-state country names in the database.
    */
    'choropleth_map_name_aliases' => [
        'Sahrawi Republic' => 'Western Sahara',
    ],

    // Legacy keys kept for backward compatibility.
    'africa_topology_url' => env(
        'AFRICA_MAP_TOPOLOGY_URL',
        $topologyBase.'custom/africa-sadr.topo.json'
    ),
    'africa_topology_version' => $topologyVersion,

];
