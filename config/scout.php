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
                    'author_name',
                    'author_affiliation',
                    'tag_names',
                    'country_names',
                    'thematic_area',
                    'sub_thematic_area',
                    'data_category_name',
                ],
                'filterableAttributes' => [
                    'sub_thematic_area_id',
                    'thematic_area_id',
                    'publication_catgory_id',
                    'data_category_id',
                    'author_id',
                    'file_type_id',
                    'tag_ids',
                    'is_featured',
                ],
                'sortableAttributes' => [
                    'title',
                    'date_created',
                ],
                'rankingRules' => [
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'sort',
                    'exactness',
                ],
                'typoTolerance' => [
                    'enabled' => true,
                    'minWordSizeForTypos' => [
                        'oneTypo' => 4,
                        'twoTypos' => 8,
                    ],
                    'disableOnWords' => [],
                    'disableOnAttributes' => [],
                ],
            ],
        ],
    ],

];
