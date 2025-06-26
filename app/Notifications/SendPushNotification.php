<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class SendPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $message;
    protected $fcmTokens;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param array $fcmTokens
     */
    public function __construct($title, $message, $fcmTokens)
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcmTokens = $fcmTokens;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Send the notification via Firebase.
     *
     * @param mixed $notifiable
     */
    public function toFirebase($notifiable)
    {
        $messaging = app('firebase.messaging');
        $count = 0;
            
        foreach($this->fcmTokens as $deviceToken):
        
            $fcm_message = CloudMessage::withTarget('token', $deviceToken);

            if($count > 0)
                $fcm_message = $fcm_message->withChangedTarget('token', $deviceToken);
          
            $fcm_message->withNotification(['title' => $this->title, 'body' => $this->message]);

            $messaging->send($fcm_message);

            $count ++;

        endforeach;
    }
    
}
