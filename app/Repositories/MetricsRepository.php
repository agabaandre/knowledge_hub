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

        $labels = [];
        $values = [];
        $iso2 = [];
        $iso3 = [];
        foreach ($records as $row) {
            $code = strtoupper(trim((string) $row->country));
            if ($code === '' || $code === 'UNKNOWN') {
                continue;
            }
            // Access logs store ISO 3166-1 alpha-2 from geo IP (e.g. US, GB, ET).
            if (strlen($code) !== 2 || ! ctype_alpha($code)) {
                continue;
            }
            $alpha3 = $this->iso2ToIso3($code);
            $iso2[] = strtolower($code);
            $iso3[] = $alpha3 ?? '';
            $labels[] = $this->iso2ToCountryName($code);
            $values[] = (int) $row->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'iso2' => $iso2,
            'iso3' => $iso3,
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

        return $query->groupBy('country')
            ->select('country', DB::raw('count(id) as count'))
            ->orderByDesc('count')
            ->get()
            ->map(function ($row) {
                $code = strtoupper((string) $row->country);
                return [
                    'code' => $code,
                    'name' => $this->iso2ToCountryName($code),
                    'count' => (int) $row->count,
                ];
            })
            ->values()
            ->all();
    }

    private function iso2ToIso3(string $iso2): ?string
    {
        $iso2 = strtoupper(trim($iso2));
        if ($iso2 === '' || strlen($iso2) !== 2) {
            return null;
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
