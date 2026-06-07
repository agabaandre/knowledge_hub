<?php

return [
    'default_course_image' => env(
        'LEARNING_DEFAULT_COURSE_IMAGE',
        'https://img.freepik.com/free-vector/digital-online-education-concept-blank-space-laptop_255625-422.jpg?semt=ais_items_boosted&w=740'
    ),

    'providers' => [
        'moodle' => \App\Services\MoodleService::class,
        'frappe' => \App\Services\FrappeLmsService::class,
        'openedx' => \App\Services\OpenEdxService::class,
    ],
];
