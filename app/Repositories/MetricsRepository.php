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
        foreach ($records as $row) {
            $code = strtoupper(trim((string) $row->country));
            if ($code === '' || $code === 'UNKNOWN') {
                continue;
            }
            $iso2[] = strtolower($code);
            $labels[] = $this->iso2ToCountryName($code);
            $values[] = (int) $row->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'iso2' => $iso2,
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

    public function country_signups($from = null, $to = null, $country = null)
    {
        $query = User::query()
            ->groupBy('country_id')
            ->select('country_id', 'country.name', DB::raw('count(users.id) as count'))
            ->join('country', 'country.id', '=', 'users.country_id')
            ->where('country.national', 'National');
        if ($from) {
            $query->whereDate('users.created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('users.created_at', '<=', $to);
        }
        if ($country) {
            $query->where('country.name', $country);
        }
        $records = $query->get();
        return [
            'labels' => $records->pluck('name')->toArray(),
            'values' => $records->pluck('count')->toArray(),
            'chartType' => 'pie',
        ];
    }

    public function monthly_signups($from = null, $to = null)
    {
        $query = User::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $records = $query
            ->groupBy(DB::raw("CONCAT(MONTHNAME(users.created_at),',',YEAR(users.created_at))"))
            ->select(DB::raw("CONCAT(MONTHNAME(users.created_at),',',YEAR(users.created_at)) as month"), DB::raw('count(users.id) as count'))
            ->get();
        return [
            'labels' => $records->pluck('month')->toArray(),
            'values' => $records->pluck('count')->toArray(),
            'chartType' => 'pie',
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
