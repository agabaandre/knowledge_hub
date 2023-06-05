<?php

namespace App\Jobs;

use App\Models\AccessLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AccessLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $ip_address;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ip)
    {
        //
        $this->ip_address = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_ip_address_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$this->ip_address)); // CALLING THE API

        Log::info(json_encode($user_ip_address_info));

        $visitorInfo = [
        'Country Code'=>$user_ip_address_info->geoplugin_countryCode
        ,'CountryName'=>$user_ip_address_info->geoplugin_countryName 
        ,'City'=>$user_ip_address_info->geoplugin_city 
        ,'Region'=>$user_ip_address_info->geoplugin_region 
        ,'Latitude'=>$user_ip_address_info->geoplugin_latitude 
        ,'Longitude'=>$user_ip_address_info->geoplugin_longitude 
        ,'Time_zone'=>$user_ip_address_info->geoplugin_timezone  
        ,'ContinentCode'=>$user_ip_address_info->geoplugin_continentCode 
        ,'ContinentName'=>$user_ip_address_info->geoplugin_continentName 
        ,'CurrencyCode'=>$user_ip_address_info->geoplugin_currencyCode
        ];


        if($visitorInfo && @$user_ip_address_info->geoplugin_status==200):
        
        $data = (Object) $visitorInfo;

        $locationLog = new AccessLog();
        $locationLog->ip_address = $this->ip_address;
        $locationLog->country = $data->CountryName;
        $locationLog->city    = $data->City;
        $locationLog->lat     = $data->Latitude;
        $locationLog->long    = $data->Longitude;
        $locationLog->save();

        endif;

    }
}
