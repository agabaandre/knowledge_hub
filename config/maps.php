<?php

return [

    'default_version_id' => env('AFRICA_MAP_VERSION', 'africa-prioritisation-1.1.3'),

    /*
    | Built-in Africa map versions. Custom entries can be added in Admin > Configure > Maps.
    */
    'versions' => [
        'africa-prioritisation-1.1.3' => [
            'label' => 'Africa (Western Sahara separate) — Prioritisation v1.1.3',
            'type' => 'geojson_script',
            'key' => 'custom/africa',
            'script' => 'assets/js/maps/africa.js',
            'version' => '1.1.3',
            'join_by' => 'iso-a3',
        ],
        'africa-sadr-topo-2.3.3' => [
            'label' => 'Highcharts SADR TopoJSON v2.3.3',
            'type' => 'topojson_url',
            'topology_url' => 'https://code.highcharts.com/mapdata/2.3.3/custom/africa-sadr.topo.json',
            'version' => '2.3.3',
            'join_by' => 'hc-key',
        ],
    ],

];
