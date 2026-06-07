<?php
namespace App\Repositories;

use App\Models\AccessLog;
use App\Models\Publication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MetricsRepository
{
    /**
     * Best-effort signup instant: created_at, falling back to email_verified_at for legacy rows.
     */
    private function signupTimestampSql(): string
    {
        return 'COALESCE(users.created_at, users.email_verified_at)';
    }

    private function applySignupDateFilters($query, ?string $from, ?string $to): void
    {
        $ts = $this->signupTimestampSql();
        if ($from) {
            $query->whereRaw("DATE({$ts}) >= ?", [$from]);
        }
        if ($to) {
            $query->whereRaw("DATE({$ts}) <= ?", [$to]);
        }
    }

    private function applySignupCountryFilter($query, ?string $country): void
    {
        if (! $country) {
            return;
        }
        $code = strtoupper(trim($country));
        if ($code === '') {
            return;
        }
        $query->whereHas('country', function ($q) use ($code) {
            $q->where('iso_code', $code);
        });
    }

    public function country_access($from = null, $to = null, $country = null)
    {
        $query = AccessLog::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($country) {
            $query->where('country', strtoupper($country));
        }
        $records = $query->groupBy('country')
            ->select('country', DB::raw('count(id) as count'))
            ->orderByDesc('count')
            ->get();

        $aggregated = [];
        foreach ($records as $row) {
            $normalized = $this->normalizeVisitCountryCode((string) $row->country);
            if ($normalized === null) {
                continue;
            }
            $key = $normalized['iso2'];
            if (! isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'iso2' => $key,
                    'iso3' => $normalized['iso3'] ?? '',
                    'label' => $normalized['label'],
                    'value' => 0,
                ];
            }
            $aggregated[$key]['value'] += (int) $row->count;
        }

        uasort($aggregated, fn (array $a, array $b): int => $b['value'] <=> $a['value']);
        $rows = array_values($aggregated);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
            'iso2' => array_column($rows, 'iso2'),
            'iso3' => array_map(fn (array $row): string => $row['iso3'] ?? '', $rows),
            'map_points' => array_map(fn (array $row): array => [
                'hc-key' => $row['iso2'],
                'iso-a2' => strtoupper($row['iso2']),
                'iso-a3' => $row['iso3'] ?: null,
                'name' => $row['label'],
                'value' => $row['value'],
            ], $rows),
            'chartType' => 'map',
            'renderAsChart' => false,
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    public function visits_over_time($from = null, $to = null, $country = null)
    {
        $query = AccessLog::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($country) {
            $query->where('country', strtoupper($country));
        }

        $start = $from ? Carbon::parse($from) : Carbon::parse(AccessLog::query()->min('created_at') ?: now());
        $end = $to ? Carbon::parse($to) : now();
        $days = max(1, $start->diffInDays($end) + 1);
        $useDaily = $days <= 90;

        if ($useDaily) {
            $records = $query
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as period'), DB::raw('count(id) as count'))
                ->orderBy('period')
                ->get();
            $labels = $records->map(function ($row) {
                return Carbon::parse($row->period)->format('M j, Y');
            })->toArray();
        } else {
            $records = $query
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"), DB::raw('count(id) as count'))
                ->orderBy('period')
                ->get();
            $labels = $records->map(function ($row) {
                return Carbon::parse($row->period . '-01')->format('M Y');
            })->toArray();
        }

        return [
            'labels' => $labels,
            'values' => $records->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'chartType' => 'line',
            'granularity' => $useDaily ? 'daily' : 'monthly',
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    public function signups_over_time($from = null, $to = null, $country = null)
    {
        $ts = $this->signupTimestampSql();
        $query = User::query()->whereRaw("{$ts} IS NOT NULL");
        $this->applySignupDateFilters($query, $from, $to);
        $this->applySignupCountryFilter($query, $country);

        $minTs = User::query()
            ->whereRaw("{$ts} IS NOT NULL")
            ->selectRaw("MIN({$ts}) as min_ts")
            ->value('min_ts');
        $start = $from ? Carbon::parse($from) : Carbon::parse($minTs ?: now());
        $end = $to ? Carbon::parse($to) : now();
        $days = max(1, $start->diffInDays($end) + 1);
        $useDaily = $days <= 90;

        if ($useDaily) {
            $records = $query
                ->groupBy(DB::raw("DATE({$ts})"))
                ->select(DB::raw("DATE({$ts}) as period"), DB::raw('count(users.id) as count'))
                ->orderBy('period')
                ->get();
            $labels = $records->map(function ($row) {
                return Carbon::parse($row->period)->format('M j, Y');
            })->toArray();
        } else {
            $records = $query
                ->groupBy(DB::raw("DATE_FORMAT({$ts}, '%Y-%m')"))
                ->select(DB::raw("DATE_FORMAT({$ts}, '%Y-%m') as period"), DB::raw('count(users.id) as count'))
                ->orderBy('period')
                ->get();
            $labels = $records->map(function ($row) {
                return Carbon::parse($row->period.'-01')->format('M Y');
            })->toArray();
        }

        return [
            'labels' => $labels,
            'values' => $records->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'chartType' => 'line',
            'granularity' => $useDaily ? 'daily' : 'monthly',
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    public function country_signups($from = null, $to = null, $country = null)
    {
        $ts = $this->signupTimestampSql();
        $query = User::query()
            ->join('country', 'country.id', '=', 'users.country_id')
            ->where('country.national', 'National')
            ->whereRaw("{$ts} IS NOT NULL");
        $this->applySignupDateFilters($query, $from, $to);
        if ($country) {
            $query->where('country.iso_code', strtoupper(trim($country)));
        }
        $records = $query
            ->groupBy('country_id', 'country.name')
            ->select('country_id', 'country.name', DB::raw('count(users.id) as count'))
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $records->pluck('name')->toArray(),
            'values' => $records->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'chartType' => 'pie',
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    public function monthly_signups($from = null, $to = null, $country = null)
    {
        $ts = $this->signupTimestampSql();
        $query = User::query()->whereRaw("{$ts} IS NOT NULL");
        $this->applySignupDateFilters($query, $from, $to);
        $this->applySignupCountryFilter($query, $country);
        $records = $query
            ->groupBy(DB::raw("DATE_FORMAT({$ts}, '%Y-%m')"))
            ->select(DB::raw("DATE_FORMAT({$ts}, '%Y-%m') as period"), DB::raw('count(users.id) as count'))
            ->orderBy('period')
            ->get();

        return [
            'labels' => $records->map(fn ($row) => Carbon::parse($row->period.'-01')->format('M Y'))->toArray(),
            'values' => $records->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'chartType' => 'bar',
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    public function monthly_publications($from = null, $to = null)
    {
        $query = Publication::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $records = $query
            ->groupBy(DB::raw("CONCAT(MONTHNAME(publication.created_at),',',YEAR(publication.created_at))"))
            ->select(DB::raw("CONCAT(MONTHNAME(publication.created_at),',',YEAR(publication.created_at)) as month"), DB::raw('count(publication.id) as count'))
            ->get();
        return [
            'labels' => $records->pluck('month')->toArray(),
            'values' => $records->pluck('count')->toArray(),
            'chartType' => 'bar',
        ];
    }

    public function listVisitCountries($from = null, $to = null): array
    {
        $query = AccessLog::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->where('country', '!=', 'UNKNOWN');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $aggregated = [];
        foreach ($query->groupBy('country')->select('country', DB::raw('count(id) as count'))->orderByDesc('count')->get() as $row) {
            $normalized = $this->normalizeVisitCountryCode((string) $row->country);
            if ($normalized === null) {
                continue;
            }
            $key = $normalized['iso2'];
            if (! isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'code' => strtoupper($key),
                    'name' => $normalized['label'],
                    'count' => 0,
                ];
            }
            $aggregated[$key]['count'] += (int) $row->count;
        }

        uasort($aggregated, fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return array_values($aggregated);
    }

    /**
     * @return array{iso2: string, iso3: string, label: string}|null
     */
    private function normalizeVisitCountryCode(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '' || strtoupper($raw) === 'UNKNOWN') {
            return null;
        }

        $upper = strtoupper($raw);

        if (strlen($upper) === 2 && ctype_alpha($upper)) {
            return [
                'iso2' => strtolower($upper),
                'iso3' => $this->iso2ToIso3($upper) ?? '',
                'label' => $this->iso2ToCountryName($upper),
            ];
        }

        if (strlen($upper) === 3 && ctype_alpha($upper)) {
            $iso2 = $this->iso3ToIso2($upper);
            if ($iso2) {
                return [
                    'iso2' => strtolower($iso2),
                    'iso3' => $upper,
                    'label' => $this->iso2ToCountryName($iso2),
                ];
            }
        }

        $nameKey = strtolower($raw);
        $aliases = [
            'united kingdom' => 'GB',
            'united states' => 'US',
            'united states of america' => 'US',
            'russia' => 'RU',
            'south korea' => 'KR',
            'north korea' => 'KP',
            'ivory coast' => 'CI',
            'czech republic' => 'CZ',
        ];
        $fromName = $aliases[$nameKey] ?? null;
        if (! $fromName) {
            $nameMap = config('iso3166.name_to_alpha2', []);
            $fromName = is_array($nameMap) ? ($nameMap[$nameKey] ?? null) : null;
        }
        if (is_string($fromName) && strlen($fromName) === 2) {
            $iso2 = strtoupper($fromName);

            return [
                'iso2' => strtolower($iso2),
                'iso3' => $this->iso2ToIso3($iso2) ?? $upper,
                'label' => $this->iso2ToCountryName($iso2),
            ];
        }

        $fromDb = \App\Models\Country::query()
            ->whereRaw('UPPER(name) = ?', [$upper])
            ->orWhereRaw('UPPER(iso_code) = ?', [$upper])
            ->orWhereRaw('UPPER(iso3_code) = ?', [$upper])
            ->first(['iso_code', 'iso3_code', 'name']);

        if ($fromDb && ! empty($fromDb->iso_code)) {
            $iso2 = strtoupper((string) $fromDb->iso_code);

            return [
                'iso2' => strtolower($iso2),
                'iso3' => strtoupper((string) ($fromDb->iso3_code ?: $this->iso2ToIso3($iso2) ?: '')),
                'label' => (string) ($fromDb->name ?: $this->iso2ToCountryName($iso2)),
            ];
        }

        return null;
    }

    private function iso3ToIso2(string $iso3): ?string
    {
        $iso3 = strtoupper(trim($iso3));
        if (strlen($iso3) !== 3) {
            return null;
        }

        $alpha3Map = config('iso3166.alpha3_to_alpha2', []);
        $fromConfig = is_array($alpha3Map) ? ($alpha3Map[$iso3] ?? null) : null;
        if (is_string($fromConfig) && strlen($fromConfig) === 2) {
            return strtoupper($fromConfig);
        }

        $fromDb = \App\Models\Country::query()
            ->where('iso3_code', $iso3)
            ->value('iso_code');

        if (is_string($fromDb) && strlen(trim($fromDb)) === 2) {
            return strtoupper(trim($fromDb));
        }

        try {
            if (class_exists(\Symfony\Component\Intl\Countries::class)) {
                return strtoupper(\Symfony\Component\Intl\Countries::getAlpha2Code($iso3));
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return null;
    }

    private function iso2ToIso3(string $iso2): ?string
    {
        $iso2 = strtoupper(trim($iso2));
        if ($iso2 === '' || strlen($iso2) !== 2) {
            return null;
        }

        $alpha2Map = config('iso3166.alpha2_to_alpha3', []);
        $fromConfig = is_array($alpha2Map) ? ($alpha2Map[$iso2] ?? null) : null;
        if (is_string($fromConfig) && strlen($fromConfig) === 3) {
            return strtoupper($fromConfig);
        }

        if (function_exists('map_iso3_from_iso2')) {
            $fromCatalog = map_iso3_from_iso2($iso2);
            if ($fromCatalog !== '') {
                return $fromCatalog;
            }
        }

        $fromDb = \App\Models\Country::query()
            ->where('iso_code', $iso2)
            ->value('iso3_code');

        if (is_string($fromDb) && trim($fromDb) !== '') {
            return strtoupper(trim($fromDb));
        }

        try {
            if (class_exists(\Symfony\Component\Intl\Countries::class)) {
                return strtoupper(\Symfony\Component\Intl\Countries::getAlpha3Code($iso2));
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return null;
    }

    private function iso2ToCountryName(string $iso2): string
    {
        $iso2 = strtoupper(trim($iso2));
        if ($iso2 === '') {
            return 'Unknown';
        }

        try {
            if (class_exists(\Symfony\Component\Intl\Countries::class)) {
                $name = \Symfony\Component\Intl\Countries::getName($iso2, 'en');
                if ($name) {
                    return $name;
                }
            }
            if (extension_loaded('intl')) {
                $name = \Locale::getDisplayRegion('-'.$iso2, 'en');
                if ($name && $name !== '-'.$iso2) {
                    return $name;
                }
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return $iso2;
    }
}
