<?php

return [

    /*
    | Africa choropleth topology (Highcharts Map Collection).
    | "africa-sadr" renders Western Sahara / SADR separately from Morocco.
    | @see https://code.highcharts.com/mapdata/
    */
    'africa_topology_url' => env(
        'AFRICA_MAP_TOPOLOGY_URL',
        'https://code.highcharts.com/mapdata/2.3.3/custom/africa-sadr.topo.json'
    ),

    'africa_topology_version' => '2.3.3',

];
