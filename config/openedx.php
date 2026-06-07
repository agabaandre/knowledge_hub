<?php

return [
    'lms_url' => env('OPENEDX_LMS_URL', ''),
    'client_id' => env('OPENEDX_CLIENT_ID', ''),
    'client_secret' => env('OPENEDX_CLIENT_SECRET', ''),
    'token_url' => env('OPENEDX_TOKEN_URL', ''),
    'course_path' => env('OPENEDX_COURSE_PATH', '/courses/{id}/about'),
    'sync_enabled' => env('OPENEDX_SYNC_ENABLED', false),
];
