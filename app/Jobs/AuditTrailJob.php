<?php

namespace App\Jobs;

use App\Models\AuditTrail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AuditTrailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $action,$description,$user_id;
    public function __construct($action,$user_id,$description)
    {
        $this->action      = $action;
        $this->user_id     = $user_id;
        $this->description = $description;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $trail = new AuditTrail();
		$trail->action      = $this->action;
		$trail->user_id     = $this->user_id;
		$trail->description = $this->description;
		$trail->save();
    }
}
