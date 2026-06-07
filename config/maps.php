<?php

$topologyVersion = env('MAP_TOPOLOGY_VERSION', '2.3.3');
$topologyBase = 'https://code.highcharts.com/mapdata/'.$topologyVersion.'/';

return [

    'default_version_id' => env('AFRICA_MAP_VERSION', 'africa-sadr-topo-2.3.3'),

    'topology_version' => $topologyVersion,

    'topology_base_url' => env('MAP_TOPOLOGY_BASE_URL', 'https://code.highcharts.com/mapdata/{version}/'),

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
        'admin_rcc' => env('MAP_VIEW_ADMIN_RCC'),
        'frontend_admin_units' => env('MAP_VIEW_FRONTEND_ADMIN_UNITS'),
    ],

    /*
    | Built-in map definitions (read-only in admin UI).
    */
    'versions' => [
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
        'africa-sadr-topo-2.3.3' => [
            'label' => 'Africa SADR — Highcharts TopoJSON v2.3.3',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'africa-sadr',
            'topology_url' => $topologyBase.'custom/africa-sadr.topo.json',
            'version' => $topologyVersion,
            'join_by' => 'hc-key',
            'scope' => 'africa',
        ],
        'world-highres-topo-2.3.3' => [
            'label' => 'World high resolution — Highcharts TopoJSON v2.3.3',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'world-highres',
            'topology_url' => $topologyBase.'custom/world-highres.topo.json',
            'version' => $topologyVersion,
            'join_by' => 'iso-a3',
            'scope' => 'world',
        ],
        'world-topo-2.3.3' => [
            'label' => 'World — Highcharts TopoJSON v2.3.3',
            'provider' => 'highcharts',
            'type' => 'topojson_url',
            'topology_preset' => 'world',
            'topology_url' => $topologyBase.'custom/world.topo.json',
            'version' => $topologyVersion,
            'join_by' => 'iso-a3',
            'scope' => 'world',
        ],
        'fusion-africa-sadr-2.3.3' => [
            'label' => 'Africa SADR — FusionCharts (HC TopoJSON v2.3.3)',
            'provider' => 'fusion',
            'type' => 'topojson_url',
            'topology_preset' => 'africa-sadr',
            'topology_url' => $topologyBase.'custom/africa-sadr.topo.json',
            'version' => $topologyVersion,
            'join_by' => 'iso-a3',
            'iso_property' => 'iso-a3',
            'scope' => 'africa',
        ],
        'fusion-world-highres-2.3.3' => [
            'label' => 'World high res — FusionCharts (HC TopoJSON v2.3.3)',
            'provider' => 'fusion',
            'type' => 'topojson_url',
            'topology_preset' => 'world-highres',
            'topology_url' => $topologyBase.'custom/world-highres.topo.json',
            'version' => $topologyVersion,
            'join_by' => 'iso-a3',
            'iso_property' => 'iso-a3',
            'scope' => 'world',
        ],
    ],

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

    // Legacy keys kept for backward compatibility.
    'africa_topology_url' => env(
        'AFRICA_MAP_TOPOLOGY_URL',
        $topologyBase.'custom/africa-sadr.topo.json'
    ),
    'africa_topology_version' => $topologyVersion,

];
