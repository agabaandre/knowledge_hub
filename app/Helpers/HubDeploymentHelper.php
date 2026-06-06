<?php

use App\Models\Country;
use Illuminate\Support\Facades\Schema;

if (! function_exists('hub_admin_units_enabled')) {
    /**
     * Whether this hub uses administrative units (country portal) vs continental portal.
     * Setting row overrides .env when the column exists and is non-null.
     */
    function hub_admin_units_enabled(): bool
    {
        try {
            $settings = function_exists('settings') ? settings() : null;
            if ($settings && Schema::hasColumn('setting', 'admin_units_enabled') && $settings->admin_units_enabled !== null) {
                return (bool) $settings->admin_units_enabled;
            }
        } catch (\Throwable $e) {
            // fall through to env
        }

        return (bool) config('deployment.admin_units_enabled', false);
    }
}

if (! function_exists('hub_owner_country_id')) {
    function hub_owner_country_id(): ?int
    {
        $fromEnv = env('HUB_OWNER_COUNTRY_ID');
        if ($fromEnv !== null && $fromEnv !== '') {
            return (int) $fromEnv;
        }

        try {
            $settings = function_exists('settings') ? settings() : null;
            if ($settings && Schema::hasColumn('setting', 'default_owner_country_id') && $settings->default_owner_country_id) {
                return (int) $settings->default_owner_country_id;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}

if (! function_exists('hub_owner_region_id')) {
    function hub_owner_region_id(): ?int
    {
        $fromEnv = env('HUB_OWNER_REGION_ID');
        if ($fromEnv !== null && $fromEnv !== '') {
            return (int) $fromEnv;
        }

        try {
            $settings = function_exists('settings') ? settings() : null;
            if ($settings && Schema::hasColumn('setting', 'default_owner_region_id') && $settings->default_owner_region_id) {
                return (int) $settings->default_owner_region_id;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $countryId = hub_owner_country_id();
        if ($countryId) {
            $country = Country::find($countryId);

            return $country && $country->region_id ? (int) $country->region_id : null;
        }

        return null;
    }
}

if (! function_exists('public_availability_default')) {
    /** Continental portals default to publicly federated content. */
    function public_availability_default(): bool
    {
        return ! hub_admin_units_enabled();
    }
}

if (! function_exists('federated_publication_url')) {
    function federated_publication_url(string $hubBaseUrl, array|object $item): string
    {
        $base = rtrim($hubBaseUrl, '/');
        $id = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
        $slug = is_array($item) ? ($item['slug'] ?? null) : ($item->slug ?? null);

        if ($slug) {
            return $base.'/records/resource/'.$slug;
        }

        if ($id) {
            return $base.'/records/resource?id='.(int) $id;
        }

        return $base.'/records';
    }
}

if (! function_exists('federated_forum_url')) {
    function federated_forum_url(string $hubBaseUrl, array|object $item): string
    {
        $base = rtrim($hubBaseUrl, '/');
        $id = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
        $slug = is_array($item) ? ($item['slug'] ?? null) : ($item->slug ?? null);

        if ($slug) {
            return $base.'/forums/thread/'.$slug;
        }

        if ($id) {
            return $base.'/forums/thread?id='.(int) $id;
        }

        return $base.'/forums';
    }
}

if (! function_exists('federation_consumer_enabled')) {
    function federation_consumer_enabled(): bool
    {
        return app(\App\Services\FederatedContentService::class)->federationConsumerEnabled();
    }
}

if (! function_exists('resolve_public_availability_from_request')) {
    function resolve_public_availability_from_request($request, $existing = null): int
    {
        if (! hub_admin_units_enabled()) {
            return 1;
        }

        if ($request->has('public_availability')) {
            return $request->boolean('public_availability') ? 1 : 0;
        }

        if ($existing !== null) {
            return (int) $existing;
        }

        return public_availability_default() ? 1 : 0;
    }
}
