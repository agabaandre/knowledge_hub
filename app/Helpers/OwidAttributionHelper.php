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
            'indicator_duplicate_groups' => count(app(\App\Services\Kpi\KpiDeduplicationService::class)->findIndicatorDuplicateGroups()),
            'subject_area_duplicate_groups' => count(app(\App\Services\Kpi\KpiDeduplicationService::class)->findSubjectAreaDuplicateGroups()),
        ];
    }
}

if (! function_exists('kpi_owid_auto_fetch_enabled')) {
    function kpi_owid_auto_fetch_enabled(): bool
    {
        if (! function_exists('settings')) {
            return true;
        }

        $value = settings()->kpi_owid_auto_fetch_enabled ?? null;

        return $value === null ? true : (bool) $value;
    }
}

if (! function_exists('kpi_recommended_defaults_list')) {
    /**
     * Curated default indicators for the publish-recommended modal.
     *
     * @return array<int, array{slug: string, kpi_id: int|null, name: string, status: string, discovered: bool, url: string|null, subject_area: string|null}>
     */
    function kpi_recommended_defaults_list(): array
    {
        $slugs = array_values(array_unique(array_filter(
            config('owid.default_published_chart_slugs', []),
            fn ($slug) => is_string($slug) && trim($slug) !== ''
        )));

        if ($slugs === []) {
            return [];
        }

        $kpis = \App\Models\Kpi::query()
            ->with('subjectArea')
            ->whereIn('owid_chart_slug', $slugs)
            ->get()
            ->keyBy('owid_chart_slug');

        return collect($slugs)->map(function (string $slug) use ($kpis) {
            $kpi = $kpis->get($slug);
            $label = $kpi?->name ?? ucwords(str_replace('-', ' ', $slug));

            return [
                'slug' => $slug,
                'kpi_id' => $kpi?->id,
                'name' => $label,
                'status' => $kpi?->status ?? 'missing',
                'discovered' => $kpi !== null,
                'url' => owid_chart_url($slug),
                'subject_area' => $kpi?->subjectArea?->name,
            ];
        })->values()->all();
    }
}

if (! function_exists('kpi_manual_data_only')) {
    function kpi_manual_data_only(): bool
    {
        if (! function_exists('settings')) {
            return false;
        }

        return (bool) (settings()->kpi_manual_data_only ?? false);
    }
}

if (! function_exists('kpi_parse_unit_label')) {
    /**
     * Derive a display unit from an OWID CSV column header stored in unit_label.
     *
     * @return array{type: string, short: string, full: string, currency_symbol: ?string}
     */
    function kpi_parse_unit_label(?string $unitLabel): array
    {
        $defaults = [
            'type' => 'other',
            'short' => '',
            'full' => '',
            'currency_symbol' => null,
        ];

        $unitLabel = trim((string) $unitLabel);
        if ($unitLabel === '') {
            return $defaults;
        }

        $full = $unitLabel;
        if (preg_match('/\(([^)]+)\)\s*$/', $unitLabel, $matches)) {
            $full = trim($matches[1]);
        } elseif (preg_match('/\(([^)]+)\)/', $unitLabel, $matches)) {
            $full = trim($matches[1]);
        }

        $lower = strtolower($full);

        if (str_contains($full, '%')
            || str_contains($lower, 'percent')
            || preg_match('/\bshare\b/', $lower)) {
            return [
                'type' => 'percent',
                'short' => '%',
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        if (preg_match('/international-?\$/i', $full)) {
            return [
                'type' => 'currency',
                'short' => 'int\'l $',
                'full' => $full,
                'currency_symbol' => 'int\'l $',
            ];
        }

        if (preg_match('/\bUS\$/i', $full) || preg_match('/\busd\b/i', $lower)) {
            return [
                'type' => 'currency',
                'short' => 'US$',
                'full' => $full,
                'currency_symbol' => 'US$',
            ];
        }

        if (preg_match('/\$/', $full)) {
            return [
                'type' => 'currency',
                'short' => '$',
                'full' => $full,
                'currency_symbol' => '$',
            ];
        }

        if (str_contains($lower, 'billion')) {
            return [
                'type' => 'billion',
                'short' => 'billion',
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        if (str_contains($lower, 'million')) {
            return [
                'type' => 'million',
                'short' => 'million',
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        if (str_contains($lower, 'thousand')) {
            return [
                'type' => 'thousand',
                'short' => 'thousand',
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        if ($lower === 'number' || preg_match('/\bpeople\b|\bpopulation\b|\binhabitants\b/', $lower)) {
            return [
                'type' => 'count',
                'short' => 'people',
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        if (preg_match('/\bper\s+[\d,]+/i', $full)) {
            return [
                'type' => 'rate',
                'short' => $full,
                'full' => $full,
                'currency_symbol' => null,
            ];
        }

        $short = strlen($full) > 36 ? '' : $full;

        return [
            'type' => 'other',
            'short' => $short,
            'full' => $full,
            'currency_symbol' => null,
        ];
    }
}

if (! function_exists('kpi_indicator_display')) {
    /**
     * Format a KPI value with unit metadata from OWID.
     *
     * @return array{
     *     value: string,
     *     unit: string,
     *     unit_full: string,
     *     value_with_unit: string,
     *     chart_unit: string,
     *     type: string
     * }
     */
    function kpi_indicator_display(float $value, ?string $unitLabel = null, ?string $kpiName = null): array
    {
        $parsed = kpi_parse_unit_label($unitLabel);
        $kpiNameLower = strtolower(trim((string) $kpiName));
        $abs = abs($value);

        if (str_contains($kpiNameLower, 'population')
            && ! in_array($parsed['type'], ['million', 'billion', 'thousand', 'percent'], true)) {
            $parsed['type'] = 'count';
            $parsed['short'] = 'people';
        }

        switch ($parsed['type']) {
            case 'percent':
                $decimals = $abs >= 10 ? 1 : 2;
                $formatted = number_format($value, $decimals);
                $unit = '%';
                break;

            case 'currency':
                $decimals = $abs >= 10_000 ? 0 : 2;
                $formatted = number_format($value, $decimals);
                $unit = (string) ($parsed['currency_symbol'] ?? $parsed['short']);
                break;

            case 'million':
                $decimals = $abs >= 100 ? 1 : ($abs >= 10 ? 2 : 3);
                $formatted = number_format($value, $decimals);
                $unit = 'million';
                break;

            case 'billion':
                $decimals = $abs >= 100 ? 1 : 2;
                $formatted = number_format($value, $decimals);
                $unit = 'billion';
                break;

            case 'thousand':
                $decimals = $abs >= 100 ? 1 : 2;
                $formatted = number_format($value, $decimals);
                $unit = 'thousand';
                break;

            case 'count':
                $formatted = number_format($value, 0);
                $unit = $parsed['short'] !== '' ? $parsed['short'] : 'people';
                break;

            case 'rate':
                $decimals = $abs >= 100 ? 1 : 2;
                $formatted = number_format($value, $decimals);
                $unit = $parsed['short'];
                break;

            default:
                $decimals = $abs >= 1_000_000 ? 0 : ($abs >= 10_000 ? 0 : ($abs >= 100 ? 1 : 2));
                $formatted = number_format($value, $decimals);
                $unit = $parsed['short'];
                break;
        }

        $unitFull = $parsed['full'] !== '' ? $parsed['full'] : $unit;
        $valueWithUnit = trim($formatted.($unit !== '' ? ' '.$unit : ''));

        return [
            'value' => $formatted,
            'unit' => $unit,
            'unit_full' => $unitFull,
            'value_with_unit' => $valueWithUnit,
            'chart_unit' => $unitFull !== '' ? $unitFull : ($unit !== '' ? $unit : 'Value'),
            'type' => $parsed['type'],
        ];
    }
}
