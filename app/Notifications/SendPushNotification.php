<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

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
     * @return \Kutia\Larafirebase\Messages\FirebaseMessage
     */
    public function toFirebase($notifiable)
    {
        $firebaseMessage = (new FirebaseMessage)
            ->withTitle($this->title)
            ->withBody($this->message)
            ->withPriority('high')
            ->asNotification($this->fcmTokens);

        return $firebaseMessage; // Ensure the correct type is returned
    }
    
}
