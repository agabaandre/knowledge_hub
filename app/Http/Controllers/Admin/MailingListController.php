<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\DB;

class MailingListController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscribe::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Statistics
        $stats = [
            'total' => Subscribe::count(),
            'subscribed' => Subscribe::where('status', 'subscribed')->count(),
            'unsubscribed' => Subscribe::where('status', 'unsubscribed')->count(),
        ];

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(25);
        $subscribers->appends($request->all());

        return view('admin.mailing_list.index', compact('subscribers', 'stats'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribes,email',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subscriber = Subscribe::create([
            'email' => $request->email,
            'name' => $request->name,
            'status' => 'subscribed',
        ]);

        return back()->with('success', 'Subscriber added successfully.');
    }

    public function update(Request $request, $id)
    {
        $subscriber = Subscribe::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribes,email,' . $id,
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:subscribed,unsubscribed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subscriber->update($request->only(['email', 'name', 'status']));

        return back()->with('success', 'Subscriber updated successfully.');
    }

    public function destroy($id)
    {
        $subscriber = Subscribe::findOrFail($id);
        $subscriber->delete();

        return back()->with('success', 'Subscriber deleted successfully.');
    }

    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required|in:all,subscribed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get recipients based on selection
        $recipients = $request->recipients === 'all' 
            ? Subscribe::all() 
            : Subscribe::where('status', 'subscribed')->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $subscriber) {
            try {
                $mail = [
                    'email' => $subscriber->email,
                    'subject' => $request->subject,
                    'body' => view('emails.newsletter', [
                        'subscriber' => $subscriber,
                        'content' => $request->message,
                        'subject' => $request->subject,
                    ])->render(),
                ];

                SendMailJob::dispatch($mail)->onQueue('default');
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to queue email to ' . $subscriber->email . ': ' . $e->getMessage());
                $failedCount++;
            }
        }

        $message = "Emails queued successfully. {$sentCount} emails queued, {$failedCount} failed.";
        return back()->with('success', $message);
    }

    public function export(Request $request)
    {
        $query = Subscribe::query();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $subscribers = $query->orderBy('email')->get();

        $filename = 'mailing_list_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Email', 'Name', 'Status', 'Subscribed Date']);

            // Add data rows
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name ?? '',
                    $subscriber->status,
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:subscribe,unsubscribe,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:subscribes,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $subscribers = Subscribe::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'subscribe':
                $subscribers->update(['status' => 'subscribed']);
                $message = 'Selected subscribers have been subscribed.';
                break;
            case 'unsubscribe':
                $subscribers->update(['status' => 'unsubscribed']);
                $message = 'Selected subscribers have been unsubscribed.';
                break;
            case 'delete':
                $subscribers->delete();
                $message = 'Selected subscribers have been deleted.';
                break;
        }

        return back()->with('success', $message);
    }
}
