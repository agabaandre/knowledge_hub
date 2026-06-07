<?php

return [
    'api_url' => env('MOODLE_API_URL', ''),
    'api_token' => env('MOODLE_API_TOKEN', ''),
    'base_url' => env('MOODLE_URL', ''),
    'default_course_image' => env('LEARNING_DEFAULT_COURSE_IMAGE', 'https://img.freepik.com/free-vector/digital-online-education-concept-blank-space-laptop_255625-422.jpg?semt=ais_items_boosted&w=740'),
    /*
    | Set to false in production to stop the hourly Moodle course sync so that
    | demo/seed courses are not re-imported. When false, moodle:fetch-courses
    | is not scheduled and the command no-ops if run manually.
    */
    'sync_enabled' => env('MOODLE_SYNC_ENABLED', true),
    /*
    | When true, courses whose fullname matches demo patterns are not stored
    | and any existing matching courses are deactivated (is_active = 0).
    | Use on production to hide demo courses (e.g. "Africa CDC eLearning Demo",
    | "IT Officer P2", "Knowledge Management Portal") from the courses page.
    */
    'exclude_demo_courses' => env('MOODLE_EXCLUDE_DEMO_COURSES', false),
    'exclude_course_name_patterns' => [
        'Africa CDC eLearning Demo',
        'IT Officer P2 (French)',
        'IT Officer P2 (English)',
        'Knowledge Management Portal',
        'Leveraging the Knowledge Hub Portal',
    ],
];
