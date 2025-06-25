<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticLink;

class StaticLinksController extends Controller
{
    public function index()
    {
        $links = StaticLink::orderBy('order')->get();
        return view('admin.static_links.index', compact('links'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
            'link' => 'required|url',
            'open_in_new_tab' => 'boolean',
        ]);
        $validated['open_in_new_tab'] = $request->has('open_in_new_tab') ? (bool)$request->open_in_new_tab : false;
        StaticLink::create($validated);
        return redirect()->back()->with('success', 'Link added successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
            'link' => 'required|url',
            'open_in_new_tab' => 'boolean',
        ]);
        $validated['open_in_new_tab'] = $request->has('open_in_new_tab') ? (bool)$request->open_in_new_tab : false;
        $link = StaticLink::findOrFail($id);
        $link->update($validated);
        return redirect()->back()->with('success', 'Link updated successfully.');
    }

    public function destroy($id)
    {
        $link = StaticLink::findOrFail($id);
        $link->delete();
        return redirect()->back()->with('success', 'Link deleted successfully.');
    }
} 