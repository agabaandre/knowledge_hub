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
    
    private $ip_address,$request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ip,$request)
    {
        //
        $this->ip_address = $ip; //"197.239.5.102"
        $this->request    = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       // $user_ip_address_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$this->ip_address)); // CALLING THE API
        try{
        $apiUrl = "http://ipinfo.io/{$this->ip_address}/json"; // Construct the query URL

        // Use file_get_contents to fetch the data
        $response = file_get_contents($apiUrl);
        $geoData  = json_decode($response); 

        Log::info(json_encode($response));

        if(@$geoData->country){

        $visitorInfo = [
        'Country Code'=>$geoData->country
        ,'CountryName'=>$geoData->country //explode('/',$geoData->timezone)[0]
        ,'City'=>$geoData->city
        ,'Region'=>$geoData->region 
        ,'Latitude'=>explode(',',$geoData->loc)[0] 
        ,'Longitude'=>explode(',',$geoData->loc)[1]
        ,'Time_zone'=>$geoData->timezone  
        //,'ContinentCode'=>$user_ip_address_info->geoplugin_continentCode 
       // ,'ContinentName'=>$user_ip_address_info->geoplugin_continentName 
       // ,'CurrencyCode'=>$user_ip_address_info->geoplugin_currencyCode
        ];


        if($visitorInfo && @$geoData->country):
        
        $data = (Object) $visitorInfo;

        $locationLog = new AccessLog();
        $locationLog->ip_address = $this->ip_address;
        $locationLog->country = $data->CountryName;
        $locationLog->city    = $data->City;
        $locationLog->lat     = $data->Latitude;
        $locationLog->long    = $data->Longitude;
        $locationLog->publication_id = ($this->request)?$this->request['id']:null;
        $locationLog->user_id = (current_user())?current_user()->id:null;
        $locationLog->save();

        endif;
    }

    }
   catch(\Exception $exception){
    Log::info($exception->getMessage());
   }

 }

}

