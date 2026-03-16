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
        $request = (Object) $this->data;
        $result = send_email($request);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $message = $result['message'] ?? 'Email sending failed';
            \Log::error('SendMailJob: send_email failed', ['email' => $request->email ?? null, 'message' => $message]);
            throw new \RuntimeException($message);
        }

        $user = User::where('email',$request->email)->first();
        
        if($user && $user->fcm_token){
            sendPushNotification($request->subject,html_to_text($request->body),$user->fcm_token);
        }
    }
}
