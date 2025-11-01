<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentRequest;
use App\Jobs\SendMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all content requests with relationships
        $contentRequests = ContentRequest::with(['country', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
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

    /**
     * Process a content request - mark as processed and send email to requester
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'content_links' => 'required|string|min:10',
            'admin_comments' => 'nullable|string|max:1000',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);

        // Update the content request with processing information
        $contentRequest->update([
            'processed_at' => now(),
            'processed_by' => Auth::id(),
            'content_links' => $request->content_links,
            'admin_comments' => $request->admin_comments,
        ]);

        // Send email to requester
        if ($contentRequest->email) {
            $emailData = [
                'to' => $contentRequest->email,
                'subject' => 'Your Content Request Has Been Processed - ' . $contentRequest->subject,
                'title' => 'Content Request Processed',
                'body' => view('emails.content_request_processed', [
                    'contentRequest' => $contentRequest,
                    'contentLinks' => $request->content_links,
                    'adminComments' => $request->admin_comments,
                ])->render(),
            ];

            SendMailJob::dispatch($emailData)->onQueue('default');
        }

        return redirect()->route('admin.content-requests.index')
            ->with('success', 'Content request processed successfully and email sent to requester.');
    }
}
