<?php

return [

    /*
    | Enable destructive wipe of publications, forums, comments, and communities.
    | Intended for freshly provisioned country hubs clearing continental seed/copy leftovers.
    | Never enable on the continental production portal.
    */
    'allow_content_wipe' => filter_var(env('HUB_ALLOW_CONTENT_WIPE', false), FILTER_VALIDATE_BOOLEAN),

    /*
    | Confirmation phrase the admin must type in the UI / pass to the artisan command.
    */
    'wipe_confirmation_phrase' => env('HUB_CONTENT_WIPE_PHRASE', 'WIPE'),

];
