<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;

class LicensesController extends Controller
{
    /**
     * Display a listing of licenses.
     */
    public function index()
    {
        $licenses = License::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new license.
     */
    public function create()
    {
        return view('admin.licenses.create');
    }

    /**
     * Store a newly created license.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:licenses,name',
            'short_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        License::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'url' => $request->url,
            'is_active' => $request->has('is_active') ? true : false,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License created successfully.');
    }

    /**
     * Show the form for editing the specified license.
     */
    public function edit($id)
    {
        $license = License::findOrFail($id);
        return view('admin.licenses.edit', compact('license'));
    }

    /**
     * Update the specified license.
     */
    public function update(Request $request, $id)
    {
        $license = License::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:licenses,name,' . $id,
            'short_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $license->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'url' => $request->url,
            'is_active' => $request->has('is_active') ? true : false,
            'sort_order' => $request->sort_order ?? $license->sort_order,
        ]);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License updated successfully.');
    }

    /**
     * Remove the specified license.
     */
    public function destroy($id)
    {
        $license = License::findOrFail($id);
        
        // Check if license is being used
        if ($license->publications()->count() > 0) {
            return redirect()->route('admin.licenses.index')
                ->with('error', 'Cannot delete license that is in use by publications.');
        }

        $license->delete();

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License deleted successfully.');
    }
}
