<?php

return [

    'states_enabled' => filter_var(env('STATES_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
    'admin_units_enabled' => filter_var(env('ADMIN_UNITS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'hub_owner_country_id' => env('HUB_OWNER_COUNTRY_ID'),
    'hub_owner_region_id' => env('HUB_OWNER_REGION_ID'),
    'federation_api_token' => env('FEDERATION_API_TOKEN'),

];