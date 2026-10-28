<?php

namespace App\Http\Controllers;

use App\Mail\Subscribe as MailSubscribe;
use App\Mail\Unsubscribe;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Subscription extends Controller
{
    // Get all email subscribers
    public function index() {

    }

    // Subscribe to newsletter and send out email
    public function subscribe(Request $request) {

        $validateData = Validator::make($request->all(), [
            'email' => [
                'required',
                'email' => 'unique:subscribes'
            ]
        ]);

        if($validateData->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $validateData->errors()->first()
            ], 500);
        }

        $subscriber = Subscribe::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Send subscription email via queue system (which uses Exchange)
        try {
            $locale = resolve_mail_locale($request->email);
            $mailData = [
                'email' => $request->email,
                'subject' => with_mail_locale($locale, static function () {
                    return __('emails.subscribed_title');
                }),
                'body' => render_mail_view('emails.subscribed', ['subscriber' => $subscriber], $locale, $request->email),
                'title' => with_mail_locale($locale, static function () {
                    return __('emails.subscribed_title');
                }),
            ];
            
            \App\Jobs\SendMailJob::dispatch($mailData)->onQueue('default');
        } catch (\Exception $e) {
            \Log::error('Exception queuing subscription email: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'You have been subscribed'
        ]);
    }

    // Unsubscribe from newsletter
    public function unsubscribe(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Change state and send out email
        $subscriber = Subscribe::where('email', $request->email)->first();
        if($subscriber != null) {
            $subscriber->status = 'unsubscribed';
            $subscriber->save();

            // Send unsubscription email via queue system (which uses Exchange)
            try {
                $locale = resolve_mail_locale($request->email);
                $mailData = [
                    'email' => $request->email,
                    'subject' => with_mail_locale($locale, static function () {
                        return __('emails.unsubscribed_title');
                    }),
                    'body' => render_mail_view('emails.unsubscribed', ['subscriber' => $subscriber], $locale, $request->email),
                    'title' => with_mail_locale($locale, static function () {
                        return __('emails.unsubscribed_title');
                    }),
                ];
                
                \App\Jobs\SendMailJob::dispatch($mailData)->onQueue('default');
            } catch (\Exception $e) {
                \Log::error('Exception queuing unsubscription email: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'You have been unsubscribed'
            ]);
        }
    }
}
