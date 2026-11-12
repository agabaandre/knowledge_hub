<?php

/**
 * Knowledge Hub application settings (safe with config:cache — no env() in app code).
 */
return [

    /*
    | Minutes used by view composers / lookup caches.
    */
    'cache_expiry_minutes' => (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24),

    'title_case_ai_enabled' => filter_var(env('TITLE_CASE_AI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    /*
    | Comma-separated role IDs that auto-publish moderated content.
    */
    'auto_publish_role_ids' => (string) env('AUTO_PUBLISH_ROLE_IDS', ''),

    'meilisearch_host' => (string) env('MEILISEARCH_HOST', ''),

    'deployment_type' => (string) env('HUB_DEPLOYMENT_TYPE', ''),

    'l5_openapi_server_url' => (string) env('L5_OPENAPI_SERVER_URL', ''),

];
