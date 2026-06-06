<?php

if (! function_exists('owid_site_url')) {
    function owid_site_url(): string
    {
        return rtrim((string) config('owid.attribution_url', 'https://ourworldindata.org'), '/');
    }
}

if (! function_exists('owid_license_url')) {
    function owid_license_url(): string
    {
        return (string) config('owid.license_url', 'https://creativecommons.org/licenses/by/4.0/');
    }
}

if (! function_exists('owid_chart_url')) {
    /**
     * @param  object|array|string|null  $item  KPI row, slug string, or chart URL
     */
    function owid_chart_url($item = null): ?string
    {
        if ($item === null) {
            return null;
        }

        if (is_string($item)) {
            if (strpos($item, 'http') === 0) {
                return $item;
            }

            $slug = trim($item);

            return $slug !== '' ? owid_site_url().'/grapher/'.rawurlencode($slug) : null;
        }

        if (is_array($item)) {
            if (! empty($item['owid_url'])) {
                return (string) $item['owid_url'];
            }

            return owid_chart_url($item['owid_chart_slug'] ?? null);
        }

        if (is_object($item)) {
            if (! empty($item->owid_url)) {
                return (string) $item->owid_url;
            }

            return owid_chart_url($item->owid_chart_slug ?? null);
        }

        return null;
    }
}

if (! function_exists('owid_attribution_label')) {
    function owid_attribution_label(): string
    {
        return (string) config('owid.attribution', 'Our World in Data (CC BY 4.0)');
    }
}

if (! function_exists('kpi_admin_stats')) {
    function kpi_admin_stats(): array
    {
        return [
            'indicators_total' => \App\Models\Kpi::query()->count(),
            'indicators_published' => \App\Models\Kpi::query()->where('status', 'published')->count(),
            'indicators_draft' => \App\Models\Kpi::query()->where('status', 'draft')->count(),
            'indicators_recalled' => \App\Models\Kpi::query()->where('status', 'recalled')->count(),
            'indicators_owid' => \App\Models\Kpi::query()->where('source', 'owid')->count(),
            'subject_areas' => \App\Models\SubjectArea::query()->where('is_active', true)->count(),
            'country_values' => \App\Models\KpiDataRecord::query()->count(),
            'narrations' => \App\Models\KpiNarration::query()->count(),
            'default_set_size' => count(config('owid.default_published_chart_slugs', [])),
        ];
    }
}
