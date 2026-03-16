<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RssFeed;
use Illuminate\Http\Request;

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
        RssFeed::create([
            'name' => $request->name,
            'url' => $request->url,
            'is_active' => $request->boolean('is_active', true),
        ]);
        return redirect()->route('admin.rss_feeds.index')->with('success', 'RSS feed added.');
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
}
