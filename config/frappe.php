<?php

return [
    'base_url' => env('FRAPPE_BASE_URL', ''),
    'api_key' => env('FRAPPE_API_KEY', ''),
    'api_secret' => env('FRAPPE_API_SECRET', ''),
    'course_doctype' => env('FRAPPE_COURSE_DOCTYPE', 'LMS Course'),
    'course_path' => env('FRAPPE_COURSE_PATH', '/lms/courses/{id}'),
    'sync_enabled' => env('FRAPPE_SYNC_ENABLED', false),
];
