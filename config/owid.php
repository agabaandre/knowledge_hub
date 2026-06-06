<?php

return [
    'base_url' => env('OWID_BASE_URL', 'https://ourworldindata.org'),
    'search_path' => '/api/search',
    'timeout' => (int) env('OWID_HTTP_TIMEOUT', 30),
    'charts_per_topic' => (int) env('OWID_CHARTS_PER_TOPIC', 12),
    'sync_chunk_size' => (int) env('OWID_SYNC_CHUNK_SIZE', 5),
    'attribution' => 'Our World in Data (CC BY 4.0)',
    'attribution_url' => 'https://ourworldindata.org',
    'license_url' => 'https://creativecommons.org/licenses/by/4.0/',

    /*
    | OWID search API topic names → Knowledge Hub subject areas.
    | Keys are stored on subject_areas.owid_topic; labels are display names.
    */
    'default_subject_areas' => [
        ['name' => 'Health', 'owid_topic' => 'Health', 'owid_search_query' => 'health mortality disease', 'sort_order' => 10],
        ['name' => 'Population & Demography', 'owid_topic' => null, 'owid_search_query' => 'population fertility life expectancy', 'sort_order' => 20],
        ['name' => 'Economy', 'owid_topic' => 'Economic Growth', 'owid_search_query' => 'gdp economy income', 'sort_order' => 30],
        ['name' => 'Food & Agriculture', 'owid_topic' => 'Food and Agriculture', 'owid_search_query' => 'food agriculture hunger nutrition', 'sort_order' => 40],
        ['name' => 'Energy & Environment', 'owid_topic' => 'Energy and Environment', 'owid_search_query' => 'energy environment climate emissions', 'sort_order' => 50],
        ['name' => 'Education', 'owid_topic' => null, 'owid_search_query' => 'education literacy schooling', 'sort_order' => 60],
        ['name' => 'Poverty & Development', 'owid_topic' => 'Poverty and Economic Development', 'owid_search_query' => 'poverty development inequality', 'sort_order' => 70],
        ['name' => 'Governance & Rights', 'owid_topic' => 'Human Rights and Democracy', 'owid_search_query' => 'democracy human rights governance', 'sort_order' => 80],
    ],

    /*
    | Default OWID chart slugs approved for public country pages after discovery.
    | Charts with reliable grapher CSV endpoints and broad African coverage.
    */
    'default_published_chart_slugs' => [
        'life-expectancy',
        'child-mortality',
        'maternal-mortality',
        'number-of-maternal-deaths',
        'global-vaccination-coverage',
        'share-of-children-younger-than-5-who-suffer-from-stunting',
        'proportion-using-safely-managed-drinking-water',
        'share-using-safely-managed-sanitation',
        'access-to-clean-fuels-and-technologies-for-cooking',
        'share-of-adults-who-are-overweight',
        'gdp-per-capita-worldbank',
        'unemployment-rate',
        'human-development-index',
        'prevalence-of-undernourishment',
        'daily-per-capita-caloric-supply',
        'co-emissions-per-capita',
        'share-of-the-population-with-access-to-electricity',
        'literacy',
        'share-of-population-in-extreme-poverty',
        'economic-inequality-gini-index',
        'electoral-democracy-index',
        'share-of-women-in-parliament',
    ],
];
