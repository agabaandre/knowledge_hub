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

        // Use Exchange OAuth instead of Laravel Mail facade
        try {
            $mail = new MailSubscribe($subscriber);
            $mailData = (object) [
                'email' => $request->email,
                'subject' => $mail->subject ?? 'Subscription Confirmation',
                'body' => view('emails.subscribed', ['subscriber' => $subscriber])->render()
            ];
            
            $result = send_email($mailData);
            
            if (!$result || (is_array($result) && !($result['success'] ?? false))) {
                \Log::error('Failed to send subscription email: ' . (is_array($result) ? ($result['message'] ?? 'Unknown error') : 'Unknown error'));
            }
        } catch (\Exception $e) {
            \Log::error('Exception sending subscription email: ' . $e->getMessage());
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

            // Use Exchange OAuth instead of Laravel Mail facade
            try {
                $mail = new Unsubscribe($subscriber);
                $mailData = (object) [
                    'email' => $request->email,
                    'subject' => $mail->subject ?? 'Unsubscription Confirmation',
                    'body' => view('emails.unsubscribed', ['subscriber' => $subscriber])->render()
                ];
                
                $result = send_email($mailData);
                
                if (!$result || (is_array($result) && !($result['success'] ?? false))) {
                    \Log::error('Failed to send unsubscription email: ' . (is_array($result) ? ($result['message'] ?? 'Unknown error') : 'Unknown error'));
                }
            } catch (\Exception $e) {
                \Log::error('Exception sending unsubscription email: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'You have been unsubscribed'
            ]);
        }
    }
}
