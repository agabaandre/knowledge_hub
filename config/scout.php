<?php

return [

    'driver' => env('SCOUT_DRIVER', 'collection'),

    'prefix' => env('SCOUT_PREFIX', ''),

    'queue' => env('SCOUT_QUEUE', false),

    'after_commit' => true,

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    'soft_delete' => false,

    'identify' => env('SCOUT_IDENTIFY', false),

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            'publications' => [
                'searchableAttributes' => [
                    'title',
                    'description',
                    'associated_authors',
                ],
                'filterableAttributes' => [
                    'sub_thematic_area_id',
                    'publication_catgory_id',
                    'data_category_id',
                ],
                'sortableAttributes' => [
                    'title',
                ],
            ],
        ],
    ],

];
