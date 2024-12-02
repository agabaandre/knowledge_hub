<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentRequest;
use Illuminate\Http\Request;

class ContentRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all content requests, you can add pagination or filtering as needed
        $contentRequests = ContentRequest::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.content_requests.index', compact('contentRequests'));
    }

    public function create()
    {
        // Show the form to create a new content request
        return view('admin.content_requests.create');
    }

    public function store(Request $request)
    {
        // Validate and store the new content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
        ]);

        ContentRequest::create($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request created successfully.');
    }

    public function edit($id)
    {
        // Fetch the content request for editing
        $contentRequest = ContentRequest::findOrFail($id);
        return view('admin.content_requests.edit', compact('contentRequest'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update the content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->update($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the content request
        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->delete();

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request deleted successfully.');
    }
}
