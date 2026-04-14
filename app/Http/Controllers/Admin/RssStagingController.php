<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicationStaging;
use App\Models\RssFeed;
use Illuminate\Http\Request;

class RssStagingController extends Controller
{
    public function index(Request $request)
    {
        $query = PublicationStaging::with('feed')->orderByDesc('created_at');

        if ($request->filled('feed_id')) {
            $query->where('rss_feed_id', $request->feed_id);
        }
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('processed_status', $request->status);
        } else {
            $query->where('processed_status', PublicationStaging::STATUS_PENDING);
        }

        $staging = $query->paginate(20)->appends($request->query());
        $feeds = RssFeed::orderBy('name')->get();

        return view('admin.rss_staging.index', compact('staging', 'feeds'));
    }

    public function edit($id)
    {
        $staging = PublicationStaging::with('feed')->findOrFail($id);
        if ($staging->processed_status !== PublicationStaging::STATUS_PENDING) {
            return redirect()->route('admin.rss_staging.index')->with('error', 'This item was already processed.');
        }
        $row = $staging; // wizard uses $row
        return view('admin.rss_staging.edit', compact('staging', 'row'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:2000']);
        $staging = PublicationStaging::findOrFail($id);
        if ($staging->processed_status !== PublicationStaging::STATUS_PENDING) {
            return back()->with('error', 'Already processed.');
        }
        $staging->update([
            'processed_at' => now(),
            'processed_status' => PublicationStaging::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);
        return redirect()->route('admin.rss_staging.index')->with('success', 'RSS item rejected.');
    }
}
