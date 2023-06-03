<?php

namespace App\Jobs;

use App\Models\AccessLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AccessLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $locationData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($locationData)
    {
        //
        $this->locationData = $locationData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $data =  $this->locationData;

        $locationLog = new AccessLog();
        $locationLog->ip_address = $data->ip_address;
        $locationLog->country = $data->countryName;
        $locationLog->city    = $data->cityName;
        $locationLog->lat     = $data->latitude;
        $locationLog->long    = $data->longitude;
        $locationLog->update();

    }
}
