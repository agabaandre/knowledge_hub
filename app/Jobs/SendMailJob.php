<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
        // Set queue to 'default' via the trait's method
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = is_array($this->data) ? (object) $this->data : $this->data;
        $to = $request->email ?? $request->to ?? null;
        if (!$to) {
            \Log::error('SendMailJob: no recipient (email/to) in payload', ['data_keys' => array_keys((array) $request)]);
            throw new \RuntimeException('No recipient email in mail job payload.');
        }
        \Log::info('SendMailJob: sending to recipient', ['to' => $to, 'subject' => $request->subject ?? $request->title ?? '']);
        $result = send_email($request);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $message = $result['message'] ?? 'Email sending failed';
            \Log::error('SendMailJob: send_email failed', ['to' => $to, 'message' => $message]);
            throw new \RuntimeException($message);
        }

        $user = User::where('email', $to)->first();
        
        if($user && $user->fcm_token){
            sendPushNotification($request->subject,html_to_text($request->body),$user->fcm_token);
        }
    }
}
