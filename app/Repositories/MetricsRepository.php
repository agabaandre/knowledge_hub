<?php
namespace App\Repositories;

use App\Models\AccessLog;
use App\Models\Publication;
use App\Models\User;
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
            $query->where('country', $country);
        }
        $records = $query->groupBy('country')->select('country', DB::raw('count(id) as count'))->get();
        return [
            'labels' => $records->pluck('country')->toArray(),
            'values' => $records->pluck('count')->toArray(),
            'chartType' => 'line',
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
}