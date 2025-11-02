<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\DB;

class MailingListController extends Controller
{
    public function index(Request $request)
    {
        // Get subscribers from subscribes table
        $subscribesQuery = Subscribe::query();
        
        // Get subscribed users from users table
        $usersQuery = User::where('is_subscribed', 1);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $subscribesQuery->where(function($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
            
            $usersQuery->where(function($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by status (only applies to subscribes table)
        if ($request->has('status') && $request->status !== '') {
            $subscribesQuery->where('status', $request->status);
        }

        // Get all subscribers from subscribes table
        $subscribes = $subscribesQuery->get()->map(function($sub) {
            return (object) [
                'id' => 'subscribe_' . $sub->id,
                'email' => $sub->email,
                'name' => $sub->name,
                'status' => $sub->status,
                'created_at' => $sub->created_at,
                'type' => 'subscribe',
                'original_id' => $sub->id,
            ];
        });

        // Get all subscribed users
        $subscribedUsers = $usersQuery->get()->map(function($user) {
            return (object) [
                'id' => 'user_' . $user->id,
                'email' => $user->email,
                'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                'status' => 'subscribed',
                'created_at' => $user->created_at,
                'type' => 'user',
                'original_id' => $user->id,
            ];
        });

        // Merge and remove duplicates (prioritize subscribes table if email exists in both)
        $allSubscribers = collect([]);
        $emailMap = [];

        // Add subscribes first
        foreach ($subscribes as $sub) {
            $emailMap[strtolower($sub->email)] = $sub;
            $allSubscribers->push($sub);
        }

        // Add users, but skip if email already exists
        foreach ($subscribedUsers as $user) {
            $emailKey = strtolower($user->email);
            if (!isset($emailMap[$emailKey])) {
                $emailMap[$emailKey] = $user;
                $allSubscribers->push($user);
            }
        }

        // Statistics - include both sources
        $totalSubscribes = Subscribe::count();
        $subscribedSubscribes = Subscribe::where('status', 'subscribed')->count();
        $unsubscribedSubscribes = Subscribe::where('status', 'unsubscribed')->count();
        $subscribedUsersCount = User::where('is_subscribed', 1)->count();

        $stats = [
            'total' => $totalSubscribes + $subscribedUsersCount,
            'subscribed' => $subscribedSubscribes + $subscribedUsersCount,
            'unsubscribed' => $unsubscribedSubscribes,
            'subscribes_count' => $totalSubscribes,
            'users_count' => $subscribedUsersCount,
        ];

        // Sort by created_at descending
        $allSubscribers = $allSubscribers->sortByDesc('created_at')->values();

        // Apply status filter if needed
        if ($request->has('status') && $request->status !== '') {
            $allSubscribers = $allSubscribers->filter(function($sub) use ($request) {
                return $sub->status === $request->status;
            })->values();
        }

        // Paginate manually
        $currentPage = $request->get('page', 1);
        $perPage = 25;
        $total = $allSubscribers->count();
        $items = $allSubscribers->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $subscribers = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
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

        // Get recipients from both sources
        $recipients = collect([]);
        
        if ($request->recipients === 'all') {
            // Get all from subscribes table
            $subscribes = Subscribe::all()->map(function($sub) {
                return (object) [
                    'email' => $sub->email,
                    'name' => $sub->name,
                    'type' => 'subscribe',
                ];
            });
            $recipients = $recipients->merge($subscribes);
            
            // Get all subscribed users
            $users = User::where('is_subscribed', 1)->get()->map(function($user) {
                return (object) [
                    'email' => $user->email,
                    'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                    'type' => 'user',
                ];
            });
            $recipients = $recipients->merge($users);
        } else {
            // Get only subscribed
            $subscribes = Subscribe::where('status', 'subscribed')->get()->map(function($sub) {
                return (object) [
                    'email' => $sub->email,
                    'name' => $sub->name,
                    'type' => 'subscribe',
                ];
            });
            $recipients = $recipients->merge($subscribes);
            
            // Get subscribed users
            $users = User::where('is_subscribed', 1)->get()->map(function($user) {
                return (object) [
                    'email' => $user->email,
                    'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                    'type' => 'user',
                ];
            });
            $recipients = $recipients->merge($users);
        }
        
        // Remove duplicates based on email (keep first occurrence)
        $emailMap = [];
        $uniqueRecipients = collect([]);
        foreach ($recipients as $recipient) {
            $emailKey = strtolower($recipient->email);
            if (!isset($emailMap[$emailKey])) {
                $emailMap[$emailKey] = true;
                $uniqueRecipients->push($recipient);
            }
        }
        $recipients = $uniqueRecipients;

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
        // Get subscribers from subscribes table
        $subscribesQuery = Subscribe::query();
        if ($request->has('status') && $request->status !== '') {
            $subscribesQuery->where('status', $request->status);
        }
        $subscribes = $subscribesQuery->get()->map(function($sub) {
            return (object) [
                'email' => $sub->email,
                'name' => $sub->name ?? '',
                'status' => $sub->status,
                'created_at' => $sub->created_at,
                'type' => 'Subscribe',
            ];
        });

        // Get subscribed users
        $users = User::where('is_subscribed', 1)->get()->map(function($user) {
            return (object) [
                'email' => $user->email,
                'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name) ?? '',
                'status' => 'subscribed',
                'created_at' => $user->created_at,
                'type' => 'User',
            ];
        });

        // Merge and remove duplicates
        $allSubscribers = collect([]);
        $emailMap = [];

        foreach ($subscribes as $sub) {
            $emailKey = strtolower($sub->email);
            if (!isset($emailMap[$emailKey])) {
                $emailMap[$emailKey] = true;
                $allSubscribers->push($sub);
            }
        }

        foreach ($users as $user) {
            $emailKey = strtolower($user->email);
            if (!isset($emailMap[$emailKey])) {
                $emailMap[$emailKey] = true;
                $allSubscribers->push($user);
            }
        }

        // Sort by email
        $allSubscribers = $allSubscribers->sortBy('email')->values();

        $filename = 'mailing_list_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($allSubscribers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Email', 'Name', 'Status', 'Type', 'Subscribed Date']);

            // Add data rows
            foreach ($allSubscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->status,
                    $subscriber->type,
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
