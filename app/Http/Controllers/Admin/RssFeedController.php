<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FetchRssFeedJob;
use App\Models\RssFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RssFeedController extends Controller
{
    public function index()
    {
        $feeds = RssFeed::orderBy('name')->paginate(20);
        return view('admin.rss_feeds.index', compact('feeds'));
    }

    public function create()
    {
        $feed = new RssFeed();
        return view('admin.rss_feeds.create', compact('feed'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'nullable|boolean',
        ]);
        $feed = RssFeed::create([
            'name' => $request->name,
            'url' => $request->url,
            'is_active' => $request->boolean('is_active', true),
        ]);
        // Auto-fetch once the feed is added (background, no progress UI)
        FetchRssFeedJob::dispatch(null, $feed->id);
        return redirect()->route('admin.rss_feeds.index')->with('success', 'RSS feed added. Fetch from feed has been started in the background.');
    }

    public function edit($id)
    {
        $feed = RssFeed::findOrFail($id);
        return view('admin.rss_feeds.edit', compact('feed'));
    }

    public function update(Request $request, $id)
    {
        $feed = RssFeed::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'nullable|boolean',
        ]);
        $feed->update([
            'name' => $request->name,
            'url' => $request->url,
            'is_active' => $request->boolean('is_active', true),
        ]);
        return redirect()->route('admin.rss_feeds.index')->with('success', 'RSS feed updated.');
    }

    public function destroy($id)
    {
        $feed = RssFeed::findOrFail($id);
        $feed->stagingItems()->delete();
        $feed->delete();
        return redirect()->route('admin.rss_feeds.index')->with('success', 'RSS feed deleted.');
    }

    /**
     * Start a manual fetch (all feeds or single feed) as background job with progress tracking.
     */
    public function fetchNow(Request $request)
    {
        $request->validate(['feed_id' => 'nullable|integer|exists:rss_feeds,id']);
        $runId = (string) Str::uuid();
        $feedId = $request->input('feed_id');
        \Illuminate\Support\Facades\Cache::put(FetchRssFeedJob::CACHE_PREFIX . $runId, [
            'status' => 'pending',
            'progress' => 0,
            'message' => 'Queued...',
            'created' => null,
            'skipped' => null,
            'updated_at' => now()->toIso8601String(),
        ], FetchRssFeedJob::CACHE_TTL);
        FetchRssFeedJob::dispatch($runId, $feedId);
        return response()->json(['run_id' => $runId]);
    }

    /**
     * Poll progress for a manual fetch run.
     */
    public function fetchProgress(string $runId)
    {
        $data = \Illuminate\Support\Facades\Cache::get(FetchRssFeedJob::CACHE_PREFIX . $runId);
        if (!$data) {
            return response()->json(['status' => 'unknown', 'message' => 'Run not found or expired.'])->setStatusCode(404);
        }
        return response()->json($data);
    }
}
