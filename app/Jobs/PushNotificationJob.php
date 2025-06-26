<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\PushNotification;
use App\Models\User;
use Exception;
use Log;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;
    protected $fcmTokens;
    protected $isTopic;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message, $fcmTokens, $isTopic = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcmTokens = is_array($fcmTokens) ? $fcmTokens : [$fcmTokens];
        $this->isTopic = $isTopic;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $messaging = app('firebase.messaging');
            $count = 0;

            Log::info("Message sending: " . count($this->fcmTokens));

            foreach ($this->fcmTokens as $deviceToken) {
                
                $target = $this->isTopic ? "topic" : "token";
                $targetValue = $this->isTopic ? "GENERAL" : $deviceToken;

                $fcm_message = CloudMessage::withTarget($target, $targetValue);

                if ($count > 0) {
                    $fcm_message = $fcm_message->withChangedTarget($target, $targetValue);
                }

                $fcm_message = $fcm_message->withNotification(['title' => $this->title, 'body' => $this->message]);

                try {
                    $sent = $messaging->send($fcm_message);

                    $notification = PushNotification::create([
                        'title' => $this->title,
                        'message' => $this->message,
                        'is_topic' => $this->isTopic,
                        'user_id' => ($this->isTopic) ? null : User::where('fcm_token', $deviceToken)->first()->id
                    ]);

                    $notification->save();

                    Log::info("Message sent: " . json_encode($sent));
                } catch (Exception $sendEx) {
                    Log::error('Error sending message to token ' . $deviceToken . ': ' . $sendEx->getMessage());
                    continue; // Continue with the next token
                }

                $count++;
            }

            Log::info("Total messages sent: " . $count);
        } catch (Exception $ex) {
            Log::error('Push Job Error: ' . $ex->getMessage());
        }
    }
}
