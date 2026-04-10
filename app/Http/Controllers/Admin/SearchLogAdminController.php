<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use App\Models\User;
use Illuminate\Http\Request;

class SearchLogAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = SearchLog::query()->with('user')->orderByDesc('created_at');

        if ($request->filled('term')) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim((string) $request->term)).'%';
            $q->where('term', 'like', $needle);
        }

        if ($request->filled('user_id') && is_numeric($request->user_id)) {
            $q->where('user_id', (int) $request->user_id);
        }

        $logs = $q->paginate((int) $request->get('rows', 50))->withQueryString();

        $topPhrases = SearchLog::query()
            ->selectRaw('LOWER(TRIM(term)) as phrase, COUNT(*) as search_count')
            ->whereNotNull('term')
            ->where('term', '!=', '')
            ->groupByRaw('LOWER(TRIM(term))')
            ->orderByDesc('search_count')
            ->limit(30)
            ->get();

        $tokenCounts = [];
        $sample = SearchLog::query()
            ->orderByDesc('id')
            ->limit(5000)
            ->pluck('term');
        foreach ($sample as $term) {
            foreach (SearchLog::tokenizeForAnalysis((string) $term) as $w) {
                $tokenCounts[$w] = ($tokenCounts[$w] ?? 0) + 1;
            }
        }
        arsort($tokenCounts);
        $topTokens = array_slice($tokenCounts, 0, 40, true);

        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.search_logs.index', [
            'logs' => $logs,
            'topPhrases' => $topPhrases,
            'topTokens' => $topTokens,
            'users' => $users,
        ]);
    }
}
