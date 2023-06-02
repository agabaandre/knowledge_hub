<?php

namespace App\Http\Controllers;

use App\Mail\Subscribe as MailSubscribe;
use App\Mail\Unsubscribe;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        Mail::to($request->email)->send(new MailSubscribe($subscriber));

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

            // Send out email using notification class
            Mail::to($request->email)->send(new Unsubscribe($subscriber));

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'You have been unsubscribed'
            ]);
        }
    }
}
